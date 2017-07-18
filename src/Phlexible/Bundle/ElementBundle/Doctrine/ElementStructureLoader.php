<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\ElementBundle\Model\ElementStructure;
use Phlexible\Bundle\ElementBundle\Model\ElementStructureValue;
use Phlexible\Bundle\ElementtypeBundle\Field\FieldRegistry;
use Phlexible\Bundle\ElementtypeBundle\File\Parser\XmlParser;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructure;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructureNode;

/**
 * Element version data.
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
     * @var XmlParser
     */
    private $xmlParser;

    /**
     * @param EntityManager $entityManager
     * @param FieldRegistry $fieldRegistry
     */
    public function __construct(EntityManager $entityManager, FieldRegistry $fieldRegistry)
    {
        $this->entityManager = $entityManager;
        $this->fieldRegistry = $fieldRegistry;

        $this->xmlParser = new XmlParser();
        $this->connection = $entityManager->getConnection();
    }

    /**
     * @var ElementStructure[]
     */
    private $map = [];

    /**
     * Load data.
     *
     * @param ElementVersion $elementVersion
     * @param string         $defaultLanguage
     *
     * @return ElementStructure
     */
    public function load(ElementVersion $elementVersion, $defaultLanguage = null)
    {
        $element = $elementVersion->getElement();

        $identifier = $element->getEid().'_'.$elementVersion->getVersion();

        if (isset($this->map[$identifier])) {
            return $this->map[$identifier];
        }

        $source = $elementVersion->getElementSource();

        $elementtype = $this->xmlParser->parseString($source->getXml());
        $elementtypeStructure = $elementtype->getStructure();

        $structureRows = $this->queryStructures($elementVersion);
        $dataRows = $this->queryValues($elementVersion->getElement()->getEid(), $elementVersion->getVersion());

        $structures = [
            null => $rootStructure = new ElementStructure(),
        ];

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
                    continue;
                }
                $myParentNode = $myNode;
                do {
                    $myParentNode = $elementtypeStructure->getNode($myParentNode->getParentDsId());
                } while (in_array($myParentNode->getType(), ['reference', 'referenceroot']));

                $structure = new ElementStructure();
                $structure
                    ->setDefaultLanguage($defaultLanguage)
                    ->setId($row['id'])
                    ->setDataId($row['data_id'])
                    ->setDsId($row['ds_id'])
                    ->setType($row['type'])
                    ->setName($myNode->getName()) // $row['name'])
                    ->setParentName($myParentNode->getName());
                $rootStructure->addStructure($structure);
                $structures[$row['id']] = $structure;
                if (isset($dataRows[$row['id']])) {
                    foreach ($dataRows[$row['id']] as $dataRow) {
                        $structure->setValue($this->createValue($dataRow, $elementtypeStructure));
                    }
                }
            }
        }

        if (isset($dataRows[$rootId])) {
            foreach ($dataRows[$rootId] as $dataRow) {
                $rootStructure->setValue($this->createValue($dataRow, $elementtypeStructure));
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
                            continue;
                        }
                        $myParentNode = $myNode;
                        do {
                            $myParentNode = $elementtypeStructure->getNode($myParentNode->getParentDsId());
                        } while (in_array($myParentNode->getType(), ['reference', 'referenceroot']));

                        $structure = new ElementStructure();
                        $structure
                            ->setDefaultLanguage($defaultLanguage)
                            ->setId($row['id'])
                            ->setDataId($row['data_id'])
                            ->setDsId($row['ds_id'])
                            ->setType($row['type'])
                            ->setName($myNode->getName()) // $row['name'])
                            ->setParentName($myParentNode->getName());

                        if (!isset($structures[$row['parent_id']])) {
                            continue;
                        }

                        /* @var $parentStructure ElementStructure */
                        $parentStructure = $structures[$row['parent_id']];
                        $parentStructure->addStructure($structure);

                        $structures[$row['id']] = $structure;

                        if (isset($dataRows[$row['id']])) {
                            foreach ($dataRows[$row['id']] as $dataRow) {
                                $structure->setValue($this->createValue($dataRow, $elementtypeStructure));
                            }
                        }
                    }
                }
            }
        }

        $this->map[$identifier] = $rootStructure;

        return $rootStructure;
    }

    public function clear()
    {
        $this->map = [];
    }

    /**
     * @param array                $dataRow
     * @param ElementtypeStructure $elementtypeStructure
     *
     * @return ElementStructureValue
     */
    private function createValue(array $dataRow, ElementtypeStructure $elementtypeStructure)
    {
        $field = $this->fieldRegistry->getField($dataRow['type']);
        $content = $field->unserialize($dataRow['content']);

        $name = $dataRow['name'];
        $node = $elementtypeStructure->getNode($dataRow['ds_id']);
        if ($node) {
            $name = $node->getName();
        }

        return new ElementStructureValue(
            $dataRow['id'],
            $dataRow['ds_id'],
            $dataRow['language'],
            $dataRow['type'],
            $field->getDataType(),
            $name,
            $content,
            $dataRow['options'] ? json_decode($dataRow['options'], true) : null
        );
    }

    /**
     * @param ElementVersion $elementVersion
     *
     * @return array
     */
    private function queryStructures(ElementVersion $elementVersion)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select(
                [
                    'es.id',
                    'es.data_id',
                    'es.parent_id',
                    'es.parent_ds_id',
                    'es.type',
                    'es.sort',
                    'es.ds_id',
                    'es.name',
                ]
            )
            ->from('element_structure', 'es')
            ->where($qb->expr()->eq('es.element_version_id', $elementVersion->getId()))
            ->orderBy('sort', 'ASC');

        $result = $this->connection->fetchAll($qb->getSQL());

        $data = [];
        foreach ($result as $row) {
            $data[$row['parent_ds_id']][] = $row;
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
                [
                    'esv.id',
                    'esv.ds_id',
                    'esv.language',
                    'esv.structure_id',
                    'esv.structure_ds_id',
                    'esv.name',
                    'esv.type',
                    'esv.content',
                    'esv.options',
                ]
            )
            ->from('element_structure_value', 'esv')
            ->where($qb->expr()->eq('esv.eid', $eid))
            ->andWhere($qb->expr()->eq('esv.version', $version));

        $result = $this->connection->fetchAll($qb->getSQL());

        $data = [];
        foreach ($result as $row) {
            $data[$row['structure_id']][] = $row;
        }

        return $data;
    }
}
