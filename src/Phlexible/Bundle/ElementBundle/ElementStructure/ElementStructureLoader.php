<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ElementStructure;

use Phlexible\Bundle\ElementBundle\ElementVersion\ElementVersion;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeService;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeStructure\ElementtypeStructureNode;
use Phlexible\Bundle\ElementtypeBundle\Field\FieldRegistry;
use Phlexible\Component\Database\ConnectionManager;

/**
 * Element version data
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @author Marcus Stöhr <mstoehr@brainbits.net>
 */
class ElementStructureLoader
{
    /**
     * @var \Zend_Db_Adapter_Abstract
     */
    private $db;

    /**
     * @var FieldRegistry
     */
    private $fieldRegistry;

    /**
     * @var ElementtypeService
     */
    private $elementtypeService;

    /**
     * @param ConnectionManager  $connectionManager
     * @param FieldRegistry      $fieldRegistry
     * @param ElementtypeService $elementtypeService
     */
    public function __construct(
        ConnectionManager $connectionManager,
        FieldRegistry $fieldRegistry,
        ElementtypeService $elementtypeService)
    {
        $this->db = $connectionManager->default;
        $this->fieldRegistry = $fieldRegistry;
        $this->elementtypeService = $elementtypeService;
    }

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
        if (isset($this->map[$elementVersion->getElement()->getEid() . '_' . $elementVersion->getVersion()])) {
            return $this->map[$elementVersion->getElement()->getEid() . '_' . $elementVersion->getVersion()];
        }

        $elementtype = $this->elementtypeService->findElementtype($elementVersion->getElement()->getElementtypeId());
        $elementtypeVersion = $this->elementtypeService->findElementtypeVersion(
            $elementtype,
            $elementVersion->getElementtypeVersion()
        );
        $elementtypeStructure = $this->elementtypeService->findElementtypeStructure($elementtypeVersion);

        $structureRows = $this->queryStructure($elementVersion->getElement()->getEid(), $elementVersion->getVersion());
        $dataRows = $this->queryData($elementVersion->getElement()->getEid(), $elementVersion->getVersion(), $language);

        $dummy = array(
            null => $rootStructure = new ElementStructure()
        );

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
                    ->setParentDsId($myNode->getParentDsId())
                    ->setName($row['name'])
                    ->setParentName($myParentNode->getName());
                $rootStructure->addStructure($structure);
                $dummy[$row['id']] = $structure;

                if (isset($dataRows[$row['id']])) {
                    foreach ($dataRows[$row['id']] as $dataRow) {
                        $structure->setValue(
                            new ElementStructureValue(
                                $dataRow['ds_id'],
                                $dataRow['name'],
                                $dataRow['type'],
                                $dataRow['content']
                            )
                        );
                    }
                }
            }
        }

        if (isset($dataRows[null])) {
            foreach ($dataRows[null] as $dataRow) {
                $rootStructure->setValue(
                    new ElementStructureValue(
                        $dataRow['ds_id'],
                        $dataRow['name'],
                        $dataRow['type'],
                        $dataRow['content']
                    )
                );
            }
        }

        $rii = new \RecursiveIteratorIterator($elementtypeStructure->getIterator(
        ), \RecursiveIteratorIterator::SELF_FIRST);
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
                            ->setParentDsId($myNode->getParentDsId())
                            ->setName($row['name'])
                            ->setParentName($myParentNode->getName());
                        /* @var $parentStructure ElementStructure */
                        $parentStructure = $dummy[$row['repeatable_id']];
                        $parentStructure->addStructure($structure);
                        $dummy[$row['id']] = $structure;

                        if (isset($dataRows[$row['id']])) {
                            foreach ($dataRows[$row['id']] as $dataRow) {
                                $structure->setValue(
                                    new ElementStructureValue(
                                        $dataRow['ds_id'],
                                        $dataRow['name'],
                                        $dataRow['type'],
                                        $dataRow['content']
                                    )
                                );
                            }
                        }
                    }
                }
            }
        }

        $this->map[$elementVersion->getElement()->getEid() . '_' . $elementVersion->getVersion()] = $rootStructure;

        return $rootStructure;
    }

    /**
     * @param int $eid
     * @param int $version
     *
     * @return array
     */
    protected function queryStructure($eid, $version)
    {
        $select = $this->db
            ->select()
            ->from(
                $this->db->prefix . 'element_structure',
                array(
                    'data_id AS id',
                    'repeatable_node',
                    'repeatable_id',
                    'repeatable_ds_id',
                    'sort',
                    'cnt',
                    'ds_id',
                    'name',
                )
            )
            ->where('eid = ?', $eid)
            ->where('version = ?', $version)
            ->order('sort ASC');

        $result = $this->db->fetchAll($select);

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
    protected function queryData($eid, $version, $language)
    {
        $select = $this->db
            ->select()
            ->from(
                $this->db->prefix . 'element_structure_data',
                array(
                    'ds_id',
                    'repeatable_id',
                    'repeatable_ds_id',
                    'name',
                    'type',
                    'content',
                )
            )
            ->where('eid = ?', $eid)
            ->where('version = ?', $version)
            ->where('language = ?', $language);

        $result = $this->db->fetchAll($select);

        $data = array();
        foreach ($result as $row) {
            $data[$row['repeatable_id']][] = $row;
        }

        return $data;
    }
}
