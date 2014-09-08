<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\ElementBundle\Model\ElementStructure;
use Phlexible\Bundle\ElementBundle\Model\ElementStructureValue;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeService;
use Phlexible\Bundle\ElementtypeBundle\Entity\ElementtypeStructureNode;
use Phlexible\Bundle\ElementtypeBundle\Field\FieldRegistry;

/**
 * Element version data
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @author Marcus Stöhr <mstoehr@brainbits.net>
 */
class ElementStructureLoader
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var FieldRegistry
     */
    private $fieldRegistry;

    /**
     * @var ElementtypeService
     */
    private $elementtypeService;

    /**
     * @param EntityManager      $entityManager
     * @param FieldRegistry      $fieldRegistry
     * @param ElementtypeService $elementtypeService
     */
    public function __construct(
        EntityManager $entityManager,
        FieldRegistry $fieldRegistry,
        ElementtypeService $elementtypeService)
    {
        $this->connection = $entityManager->getConnection();
        $this->fieldRegistry = $fieldRegistry;
        $this->elementtypeService = $elementtypeService;
    }

    /**
     * @var ElementStructure[]
     */
    private $map = array();

    /**
     * Load data
     *
     * @param ElementVersion $elementVersion
     * @param string         $language
     *
     * @return ElementStructure
     */
    public function load(ElementVersion $elementVersion, $language)
    {
        $identifier = $elementVersion->getElement()->getEid() . '_' . $elementVersion->getVersion() . '_' . $language;

        if (isset($this->map[$identifier])) {
            return $this->map[$identifier];
        }

        $elementtype = $this->elementtypeService->findElementtype($elementVersion->getElement()->getElementtypeId());
        $elementtypeVersion = $this->elementtypeService->findElementtypeVersion(
            $elementtype,
            $elementVersion->getElementtypeVersion()
        );
        $elementtypeStructure = $this->elementtypeService->findElementtypeStructure($elementtypeVersion);

        $structureRows = $this->queryStructures($elementVersion->getElement()->getEid(), $elementVersion->getVersion());
        $dataRows = $this->queryValues($elementVersion->getElement()->getEid(), $elementVersion->getVersion(), $language);

        $dummy = array(
            null => $rootStructure = new ElementStructure()
        );

        if (!$structureRows && !$dataRows) {
            return $rootStructure;
        }

        if (isset($structureRows[null])) {
            foreach ($structureRows[null] as $row) {
                $myNode = $elementtypeStructure->getNode($row['ds_id']);
                if (!$myNode) {
                    //throw new \Exception('Broken structure.');
                    continue;
                }
                $myParentNode = $myNode;
                do {
                    $myParentNode = $elementtypeStructure->getNode($myParentNode->getParentDsId());
                } while (in_array($myParentNode->getType(), array('reference', 'referenceroot')));


                //echo 'add ' . $row['id']." " .$row['ds_id'].PHP_EOL;
                $structure = new ElementStructure();
                $structure
                    ->setId($row['id'])
                    ->setDsId($row['ds_id'])
                    //->setParentId(null)//$myNode->getParentId())
                    //->setParentDsId(null)//$myNode->getParentDsId())
                    ->setName($row['name'])
                    ->setParentName($myParentNode->getName());
                $rootStructure->addStructure($structure);
                $dummy[$row['id']] = $structure;

                if (isset($dataRows[$row['id']])) {
                    foreach ($dataRows[$row['id']] as $dataRow) {
                        $structure->setValue($this->createValue($dataRow));
                    }
                }
            }
        }

        if (isset($dataRows[null])) {
            foreach ($dataRows[null] as $dataRow) {
                $rootStructure->setValue($this->createValue($dataRow));
            }
        }

        $rii = new \RecursiveIteratorIterator($elementtypeStructure->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($rii as $node) {
            /* @var $node ElementtypeStructureNode */
            if ($node->isRepeatable() || $node->isOptional()) {
                //echo $node->getName() . " " .$node->getId()." ".$node->getDsId().($node->isOptional() ? ' optional' : '').($node->isRepeatable() ? ' repeatable' : '').PHP_EOL;

                if (isset($structureRows[$node->getDsId()])) {
                    foreach ($structureRows[$node->getDsId()] as $row) {
                        $myNode = $elementtypeStructure->getNode($row['ds_id']);
                        if (!$myNode) {
                            //throw new \Exception('Broken structure.');
                            continue;
                        }
                        $myParentNode = $myNode;
                        do {
                            $myParentNode = $elementtypeStructure->getNode($myParentNode->getParentDsId());
                        } while (in_array($myParentNode->getType(), array('reference', 'referenceroot')));

                        //echo 'add ' . $row['id']." " .$row['ds_id'].PHP_EOL;
                        $structure = new ElementStructure();
                        $structure
                            ->setId($row['id'])
                            ->setDsId($row['ds_id'])
                            //->setParentId($myNode->getParentId())
                            //->setParentDsId($myNode->getParentDsId())
                            ->setName($row['name'])
                            ->setParentName($myParentNode->getName());
                        /* @var $parentStructure ElementStructure */

                        if (!isset($dummy[$row['repeatable_id']])) {
                            continue;
                            ldd($structure);
                            echo PHP_EOL.$node->getName()." ".$node->getDsId().PHP_EOL;
                            die;
                        }
                        $parentStructure = $dummy[$row['repeatable_id']];
                        $parentStructure->addStructure($structure);

                        $dummy[$row['id']] = $structure;

                        if (isset($dataRows[$row['id']])) {
                            foreach ($dataRows[$row['id']] as $dataRow) {
                                $structure->setValue($this->createValue($dataRow));
                            }
                        }
                    }
                }
            }
        }

        $this->map[$identifier] = $rootStructure;

        return $rootStructure;
    }

    /**
     * @param array $dataRow
     *
     * @return ElementStructureValue
     */
    private function createValue(array $dataRow)
    {
        $field = $this->fieldRegistry->getField($dataRow['type']);

        $value = $field->fromRaw($dataRow['value']);

        return new ElementStructureValue(
            $dataRow['id'],
            $dataRow['ds_id'],
            $dataRow['language'],
            $dataRow['type'],
            $dataRow['name'],
            $value,
            $dataRow['options']
        );
    }

    /**
     * @param int $eid
     * @param int $version
     *
     * @return array
     */
    private function queryStructures($eid, $version)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select(
                array(
                    'es.data_id AS id',
                    'es.repeatable_id',
                    'es.repeatable_ds_id',
                    'es.sort',
                    'es.cnt',
                    'es.ds_id',
                    'es.name',
                )
            )
            ->from('element_structure', 'es')
            ->where($qb->expr()->eq('es.eid', $eid))
            ->andWhere($qb->expr()->eq('es.version', $version))
            ->orderBy('sort', 'ASC');

        $result = $this->connection->fetchAll($qb->getSQL());

        $data = array();
        foreach ($result as $row) {
            $data[$row['repeatable_ds_id']][] = $row;
        }

        return $data;
    }

    /**
     * @param int    $eid
     * @param int    $version
     * @param string $language
     *
     * @return array
     */
    private function queryValues($eid, $version, $language)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select(
                array(
                    'esv.data_id AS id',
                    'esv.ds_id',
                    'esv.language',
                    'esv.repeatable_id',
                    'esv.repeatable_ds_id',
                    'esv.name',
                    'esv.type',
                    'esv.content AS value',
                    'esv.options',
                )
            )
            ->from('element_structure_value', 'esv')
            ->where($qb->expr()->eq('esv.eid', $eid))
            ->andWhere($qb->expr()->eq('esv.version', $version))
            ->andWhere($qb->expr()->eq('esv.language', $qb->expr()->literal($language)));

        $result = $this->connection->fetchAll($qb->getSQL());

        $data = array();
        foreach ($result as $row) {
            $data[$row['repeatable_id']][] = $row;
        }

        return $data;
    }
}
