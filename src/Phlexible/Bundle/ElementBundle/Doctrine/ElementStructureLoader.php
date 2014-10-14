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
     * @param string         $defaultLanguage
     *
     * @return ElementStructure
     */
    public function load(ElementVersion $elementVersion, $defaultLanguage = null)
    {
        $identifier = $elementVersion->getElement()->getEid() . '_' . $elementVersion->getVersion();

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
        $dataRows = $this->queryValues($elementVersion->getElement()->getEid(), $elementVersion->getVersion());

        $structures = array(
            null => $rootStructure = new ElementStructure()
        );

        $rootId = null;
        $rootStructure->setDefaultLanguage($defaultLanguage);

        if (!$structureRows && !$dataRows) {
            return $rootStructure;
        }

        if (isset($structureRows[null])) {
            foreach ($structureRows[null] as $row) {
                if ($row['type'] === 'root') {
                    $rootId = $row['id'];
                    $rootStructure
                        ->setDefaultLanguage($defaultLanguage)
                        ->setId($row['id'])
                        ->setDataId($row['data_id'])
                        ->setDsId($row['ds_id'])
                        ->setType($row['type'])
                        ->setName($row['name']);
                    continue;
                }
                $myNode = $elementtypeStructure->getNode($row['ds_id']);
                if (!$myNode) {
                    //throw new \Exception('Broken structure.');
                    continue;
                }
                $myParentNode = $myNode;
                do {
                    $myParentNode = $elementtypeStructure->getNode($myParentNode->getParentDsId());
                } while (in_array($myParentNode->getType(), array('reference', 'referenceroot')));

                $structure = new ElementStructure();
                $structure
                    ->setDefaultLanguage($defaultLanguage)
                    ->setId($row['id'])
                    ->setDataId($row['data_id'])
                    ->setDsId($row['ds_id'])
                    ->setType($row['type'])
                    ->setName($row['name'])
                    ->setParentName($myParentNode->getName());
                $rootStructure->addStructure($structure);
                $structures[$row['id']] = $structure;

                if (isset($dataRows[$row['id']])) {
                    foreach ($dataRows[$row['id']] as $dataRow) {
                        $structure->setValue($this->createValue($dataRow));
                    }
                }
            }
        }

        if (isset($dataRows[$rootId])) {
            foreach ($dataRows[$rootId] as $dataRow) {
                $rootStructure->setValue($this->createValue($dataRow));
            }
        }

        $rii = new \RecursiveIteratorIterator($elementtypeStructure->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($rii as $node) {
            /* @var $node ElementtypeStructureNode */
            if ($node->isRepeatable() || $node->isOptional()) {
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

                        $structure = new ElementStructure();
                        $structure
                            ->setDefaultLanguage($defaultLanguage)
                            ->setId($row['id'])
                            ->setDataId($row['data_id'])
                            ->setDsId($row['ds_id'])
                            ->setType($row['type'])
                            ->setName($row['name'])
                            ->setParentName($myParentNode->getName());

                        if (!isset($structures[$row['repeatable_id']])) {
                            continue;
                            ldd($structure);
                            echo PHP_EOL.$node->getName()." ".$node->getDsId().PHP_EOL;
                            die;
                        }
                        /* @var $parentStructure ElementStructure */
                        $parentStructure = $structures[$row['repeatable_id']];
                        $parentStructure->addStructure($structure);

                        $structures[$row['id']] = $structure;

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
            $field->getDataType(),
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
                    'es.id',
                    'es.data_id',
                    'es.repeatable_id',
                    'es.repeatable_ds_id',
                    'es.type',
                    'es.sort',
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
     * @param int $eid
     * @param int $version
     *
     * @return array
     */
    private function queryValues($eid, $version)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select(
                array(
                    'esv.id',
                    'esv.ds_id',
                    'esv.language',
                    'esv.structure_id',
                    'esv.structure_ds_id',
                    'esv.name',
                    'esv.type',
                    'esv.content AS value',
                    'esv.options',
                )
            )
            ->from('element_structure_value', 'esv')
            ->where($qb->expr()->eq('esv.eid', $eid))
            ->andWhere($qb->expr()->eq('esv.version', $version))
        ;

        $result = $this->connection->fetchAll($qb->getSQL());

        $data = array();
        foreach ($result as $row) {
            $data[$row['structure_id']][] = $row;
        }

        return $data;
    }
}
