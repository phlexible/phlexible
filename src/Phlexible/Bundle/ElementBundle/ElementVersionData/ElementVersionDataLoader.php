<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ElementVersionData;

use Phlexible\Bundle\ElementBundle\ElementVersion\ElementVersion;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeService;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeStructure\ElementtypeStructure;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeStructure\ElementtypeStructureIterator;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeStructure\ElementtypeStructureNode;
use Phlexible\Bundle\ElementtypeBundle\Field\FieldRegistry;
use Phlexible\Component\Database\ConnectionManager;

/**
 * Element version data
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @author Marcus Stöhr <mstoehr@brainbits.net>
 */
class ElementVersionDataLoader
{
    const CACHE_VERSION = 1;

    const MODE_BACKEND = 'backend';
    const MODE_FRONTEND = 'frontend';
    const MODE_DIFF = 'diff';

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

    private $mode = self::MODE_BACKEND;

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

    /**
     * Load data
     *
     * @param ElementVersion $elementVersion
     * @param string         $language
     *
     * @return ElementVersionData
     */
    public function load(ElementVersion $elementVersion, $language)
    {
        $elementtype = $this->elementtypeService->findElementtype($elementVersion->getElement()->getElementtypeId());
        $et = $this->elementtypeService->findElementtypeVersion($elementtype, $elementVersion->getElementtypeVersion());
        $etTree = $this->elementtypeService->findElementtypeStructure($et);

        $this->fieldClasses = array();
        $this->eid = $elementVersion->getElement()->getEid();
        $this->version = $elementVersion->getVersion();
        $this->language = $language;

        $contentFlat = $this->queryData($this->eid, $this->language, $this->version);

        $contentArray = array();

        foreach ($contentFlat as $id => $contentItem) {
            if ($contentItem['repeatable_node']) {
                $contentArray['node'][$contentItem['ds_id']][$contentItem['id']] = $contentItem;
            }

            if ($contentItem['repeatable_id'] && $contentItem['repeatable_ds_id']) {
                $contentArray['repeatable'][$contentItem['repeatable_id']][$contentItem['ds_id']][$contentItem['id']] = $contentItem;
            } else {
                $contentArray['direct'][$contentItem['ds_id']][$contentItem['id']] = $contentItem;
            }
        }

        $data = $this->recurseData($etTree, $contentArray);
        if (is_array($data)) {
            $data = array_shift($data);
        }

        $elementVersionData = new ElementVersionData();
        $elementVersionData
            ->setElementVersion($elementVersion)
            ->setLanguage($language)
            ->setTree($data)
            ->setFieldClasses($this->fieldClasses);

        unset($this->fieldClasses);
        unset($this->eid);
        unset($this->language);
        unset($this->version);

        return $elementVersionData;
    }

    protected function queryData($eid, $language, $version)
    {
        $select = $this->db
            ->select()
            ->from(
                array('ed' => $this->db->prefix . 'element_data'),
                array(
                    'data_id AS id',
                    'repeatable_node',
                    'repeatable_id',
                    'repeatable_ds_id',
                    'sort',
                    'cnt',
                    'ds_id',
                )
            )
            ->joinLeft(
                array('edl' => $this->db->prefix . 'element_data_language'),
                'ed.eid = edl.eid AND ed.version = edl.version AND ed.data_id = edl.data_id AND edl.language = ' . $this->db->quote(
                    $language
                ),
                array('content', 'content_options' => 'options')
            )
            ->where('ed.eid = ?', $eid)
            ->where('ed.version = ?', $version);

        $result = $this->db->fetchAssoc($select);

        return $result;
    }

    protected function recurseData(
        ElementtypeStructure $tree,
        array $contentArray,
        $currentDsId = null,
        $repeatableId = null)
    {
        $data = array();
        $indexCnt = 0;

        $iterator = new ElementtypeStructureIterator($tree, $currentDsId);

        if ($currentDsId === null) {
            $currentNode = $tree->getRootNode();

            if (!$currentNode) {
                return null;
            }
        } else {
            $currentNode = $tree->getNode($currentDsId);
        }

        if (!$currentNode) {
            echo '<pre>No current node!' . PHP_EOL;
            echo 'current ds id: ' . $currentDsId . PHP_EOL;
            print_r($tree);
            echo Brainbits_Debug_Backtrace::staticStackDump(true);
            die;
        }

        $sortable = false;
        if ($currentNode->getConfigurationValue('sortable') === 'on') {
            $indexCnt = 1000;
            $sortable = true;
        }

        foreach ($iterator as $node) {
            /* @var $node ElementtypeStructureNode */

            if ($node->getType() == 'reference' || $node->getType() == 'referenceroot') {
                $dummy = $tree->getChildNodes($node->getDsId());
                $node = $dummy[0];
            }

            // repetition of the block above is wanted! we need to eliminate all reference nodes.
            if ($node->getType() == 'reference' || $node->getType() == 'referenceroot') {
                $dummy = $tree->getChildNodes($node->getDsId());
                $node = $dummy[0];
            }

            $dsId = $node->getDsId();
            $type = $node->getType();

            $useNewRepeatableId = false;
            if (!empty($contentArray['node'][$dsId])) {
                $useNewRepeatableId = true;
                $childRepeatableNodes = $contentArray['node'][$dsId];
            }
            if (!empty($contentArray['repeatable'][$repeatableId][$dsId])) {
                $childRepeatableNodes = $contentArray['repeatable'][$repeatableId][$dsId];
            } elseif (!empty($contentArray['direct'][$dsId])) {
                $childRepeatableNodes = $contentArray['direct'][$dsId];
            } else {
                $childRepeatableNodes = array($repeatableId => array());
            }

            foreach ($childRepeatableNodes as $childRepeatableId => $childRepeatableNode) {
                if ($useNewRepeatableId) {
                    $newRepeatableId = $childRepeatableId;
                } else {
                    $newRepeatableId = $repeatableId;
                }

                $children = $this->recurseData($tree, $contentArray, $node->getDsId(), $newRepeatableId);

                $empty = true;
                foreach ($children as $child) {
                    if (empty($child['empty'])) {
                        $empty = false;
                        break;
                    }
                }

                $contentNode = null;
                if (!$repeatableId && !empty($contentArray['direct'][$dsId][$childRepeatableId])) {
                    $contentNode = $contentArray['direct'][$dsId][$childRepeatableId];
                } elseif ($repeatableId && !empty($contentArray['repeatable'][$repeatableId][$dsId][$childRepeatableId])) {
                    $contentNode = $contentArray['repeatable'][$repeatableId][$dsId][$childRepeatableId];
                }

                if ($type != 'reference' && $type != 'referenceroot') {
                    $dataNode = array(
                        'id'               => $node->getId(),
                        'ds_id'            => $dsId,
                        'parent_id'        => $node->getParentId(),
                        'type'             => $type,
                        //                        'data_content'     => '',
                        'working_title'    => $node->getName(),
                        'configuration'    => $node->getConfiguration(),
                        'validation'       => $node->getValidation(),
                        'labels'           => $node->getLabels(),
                        'options'          => $node->getOptions(),
                        'content_channels' => $node->getContentChannels(),
                        'data_content'     => null,
                    );

                    if ($contentNode) {
                        $dataNode['data_id'] = $contentNode['id'];
                        $dataNode['data_sort'] = $contentNode['sort'];
                        $dataNode['data_content'] = $contentNode['content'];
                        $dataNode['data_options'] = $contentNode['content_options'];
                        $dataNode['data_cnt'] = 1;

                        if (mb_strlen($dataNode['data_options'])) {
                            $dataNode['data_options'] = unserialize($dataNode['data_options']);

                            if (!empty($dataNode['data_options']['unlinked'])) {
                                $dummySelect = $this->db
                                    ->select()
                                    ->from($this->db->prefix . 'element_data_language', 'content')
                                    ->where('eid = ?', $this->eid)
                                    ->where('version = ?', $this->version)
                                    ->where('data_id = ?', $dataNode['data_id'])
                                    ->where('language = ?', 'de');

                                $dataNode['data_options']['master_value'] = $this->db->fetchOne($dummySelect);
                            }
                        }
                    }

                    if ($type != 'group' && $type != 'accordion' && $type != 'tab' && !mb_strlen(
                            $dataNode['data_content']
                        )
                    ) {
                        $dataNode['empty'] = true;
                    }

                    $field = $this->fieldRegistry->getField($dataNode['type']);

                    try {
                        $dataNode = $field->transform($dataNode, $this->eid, $this->version, $this->language);
                    } catch (\Exception $e) {
                        echo $e->getMessage() . '<pre>';
                        echo $e->getTraceAsString() . PHP_EOL;
                        print_r($dataNode);
                        die;
                    }

                    if (method_exists($field, 'front')) {
                        $this->fieldClasses[$dataNode['type']] = $field;
                    }

                    if ($children) {
                        $dataNode['children'] = $children;

                        if ($empty) {
                            $dataNode['empty'] = true;
                        }
                    }
                } else {
                    if (count($children)) {
                        reset($children);
                        $dataNode = current($children);
                    } else {
                        print_r($children);
                        die('ERROR');
                    }
                }

                if ($sortable && isset($dataNode['data_sort'])) {
                    $index = $dataNode['data_sort'];
                } else {
                    $index = $indexCnt++;
                }

                $data[$index] = $dataNode;
            }
        }

        ksort($data);

        if ($this->mode == self::MODE_FRONTEND) {
            foreach ($data as $key => $dataNode) {
                if ($dataNode['type'] == 'group' &&
                    isset($dataNode['configuration']['repeat_min']) &&
                    strlen($dataNode['configuration']['repeat_min']) &&
                    $dataNode['configuration']['repeat_min'] == 0 &&
                    empty($dataNode['data_cnt'])
                ) {
                    unset($data[$key]);
                    continue;
                }

                $repeatable = false;

                if (!empty($dataNode['configuration']['repeat_max']) && $dataNode['configuration']['repeat_max'] > 1) {
                    $repeatable = true;
                }

                unset($dataNode['data_options']);
                unset($dataNode['configuration']);
                unset($dataNode['validation']);
                unset($dataNode['help']);
                //                unset($dataNode['prefix']);
                //                unset($dataNode['suffix']);

                $workingTitle = $dataNode['working_title'];

                if ($repeatable) {
                    $workingTitle .= '_' . $key;
                }

                unset($data[$key]);
                $data[$workingTitle] = $dataNode;
            }
        } else {
            $data = array_values($data);
        }

        return $data;
    }
}
