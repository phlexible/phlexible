<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Controller\Data;

use Phlexible\Bundle\ElementBundle\ElementEvents;
use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\ElementBundle\Event\SaveElementEvent;
use Phlexible\Bundle\ElementBundle\Event\SaveNodeDataEvent;
use Phlexible\Bundle\ElementBundle\Event\SaveTeaserDataEvent;
use Phlexible\Bundle\ElementBundle\Model\ElementStructure;
use Phlexible\Bundle\ElementBundle\Model\ElementStructureValue;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructure;
use Phlexible\Bundle\TeaserBundle\Doctrine\TeaserManager;
use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TeaserBundle\Model\TeaserManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Phlexible\Bundle\TreeBundle\Tree\TreeManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Data saver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DataSaver
{
    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var TreeManager
     */
    private $treeManager;

    /**
     * @var TeaserManagerInterface
     */
    private $teaserManager;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var array
     */
    private $structures = array();

    /**
     * @param ElementService           $elementService
     * @param TreeManager              $treeManager
     * @param TeaserManagerInterface   $teaserManager
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(ElementService $elementService, TreeManager $treeManager, TeaserManagerInterface $teaserManager, EventDispatcherInterface $dispatcher)
    {
        $this->elementService = $elementService;
        $this->treeManager = $treeManager;
        $this->teaserManager = $teaserManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Save element data
     *
     * @param Request       $request
     * @param UserInterface $user
     *
     * @return ElementVersion
     */
    public function save(Request $request, UserInterface $user)
    {
        $eid = $request->get('eid');
        $language = $request->get('language');
        $tid = $request->get('tid');
        $teaserId = $request->get('teaser_id');
        $data = $request->get('data');

        $values = $request->request->all();

        if ($data) {
            $data = json_decode($data, true);
        }

        $element = $this->elementService->findElement($eid);
        $elementtype = $this->elementService->findElementtype($element);
        $elementtypeVersion = $this->elementService->getElementtypeService()->findLatestElementtypeVersion($elementtype);
        $elementtypeStructure = $this->elementService->getElementtypeService()->findElementtypeStructure($elementtypeVersion);

        $oldElementVersion = $this->elementService->findLatestElementVersion($element);
        $oldVersion = $oldElementVersion->getVersion();
        $isMaster = $element->getMasterLanguage() === $language;

        $tree = $this->treeManager->getByNodeId($tid);
        $node = $tree->get($tid);

        $teaser = null;
        if ($teaserId) {
            $teaser = $this->teaserManager->findTeaser($teaserId);
        }

        $elementComment = null;
        if (!empty($data['comment'])) {
            $elementComment = $data['comment'];
        }

        $elementVersion = clone $oldElementVersion;
        $elementVersion
            ->setVersion($elementVersion->getVersion() + 1)
            ->setCreateUserId($user->getId())
            ->setCreatedAt(new \DateTime())
            ->setComment($elementComment)
            ->setTriggerLanguage($language);

        $element->setLatestVersion($elementVersion->getVersion());

        $newVersion = $elementVersion->getVersion();

        $elementStructure = $this->createStructure($elementVersion, $elementtypeStructure, $values);

        $event = new SaveElementEvent($element, $language, $oldVersion);
        $this->dispatcher->dispatch(ElementEvents::BEFORE_SAVE_ELEMENT, $event);

        $this->elementService->updateElement($element, false);
        $this->elementService->updateElementVersion($elementVersion, false);
        $this->elementService->updateElementStructure($elementStructure, false);

        if ($teaser) {
            $this->saveTeaserData($teaser, $language, $data);
        } else {
            $this->saveNodeData($node, $language, $data);
        }

        $event = new SaveElementEvent($element, $language, $oldVersion);
        $this->dispatcher->dispatch(ElementEvents::SAVE_ELEMENT, $event);

        return $elementVersion;
    }

    /**
     * @param Teaser $teaser
     * @param string $language
     * @param array  $data
     */
    private function saveTeaserData(Teaser $teaser, $language, array $data)
    {
        $event = new SaveTeaserDataEvent($teaser, $language, $data);
        $this->dispatcher->dispatch(ElementEvents::BEFORE_SAVE_TEASER_DATA, $event);

        // save teaser

        if (!empty($data['teaser'])) {
            $updateData = array(
                'disable_cache'  => empty($data['teaser']['disable_cache']) ? 0 : 1,
                'cache_lifetime' => (int) Brainbits_Util_Array::get($data['teaser'], 'cache_lifetime', 0),
            );

            if ($updateData['disable_cache']) {
                $updateData['cache_lifetime'] = 0;
            }

            $db->update($db->prefix . 'tree_teaser', $updateData, 'id = ' . $db->quote($teaserId));
        }

        // save context

        if (isset($data['context'])) {
            $db->delete($db->prefix . 'teaser_context', array('teaser_id = ?' => $teaserId));

            $insertData = array(
                'teaser_id' => $teaserId
            );

            foreach ($data['context'] as $country) {
                $insertData['context'] = $country;

                $db->insert($db->prefix . 'teaser_context', $insertData);
            }
        }

        $event = new SaveTeaserDataEvent($teaser, $language, $data);
        $this->dispatcher->dispatch(ElementEvents::SAVE_TEASER_DATA, $event);
    }

    /**
     * @param TreeNodeInterface $node
     * @param string            $language
     * @param array             $data
     */
    private function saveNodeData(TreeNodeInterface $node, $language, array $data)
    {
        $event = new SaveNodeDataEvent($node, $language, $data);
        $this->dispatcher->dispatch(ElementEvents::BEFORE_SAVE_NODE_DATA, $event);

        // save config

        if (!empty($data['config'])) {
            if (!empty($data['config']['navigation'])) {
                $node->setAttribute('navigation', true);
            } else {
                $node->removeAttribute('navigation');
            }
            if (!empty($data['config']['needs_authentication'])) {
                $node->setAttribute('needs_authentication', true);
            } else {
                $node->removeAttribute('needs_authentication');
            }
            if (!empty($data['config']['route'])) {
                $node->setAttribute('route', true);
            } else {
                $node->removeAttribute('route');
            }
            if (!empty($data['config']['controller'])) {
                $node->setAttribute('controller', true);
            } else {
                $node->removeAttribute('controller');
            }
            if (!empty($data['config']['https'])) {
                $node->setAttribute('https', true);
            } else {
                $node->removeAttribute('https');
            }

            $tree->updateNode($node);
        }

        // save meta
        // TODO: repair save meta

        if (!empty($data['meta'])) {
            $metaSetId = $elementVersion->getElementTypeVersionObj()->getMetaSetId();
            /* @var $metaSet Media_MetaSets_Set */
            $metaSet = $container->get('metasets.repository')->find($metaSetId);

            $identifier = new Makeweb_Elements_Element_Version_MetaSet_Identifier($elementVersion, $language);
            /* @var $metaSetItem Media_MetaSets_Item */
            $metaSetItem = Media_MetaSets_Item_Peer::get($metaSetId, $identifier);
            $metaSetArray = $metaSetItem->toArray($language);

            foreach ($data['meta'] as $key => $value) {
                if (!$isMaster && $metaSetArray[$key]['synchronized']) {
                    if ($metaSetItem->$key === null) {
                        $metaSetItem->$key = '';
                    }

                    continue;
                }

                if ('suggest' === $metaSetItem->getType($key)) {
                    $dataSourceId = $metaSetItem->getOptions($key);
                    $dataSourcesRepository = $container->get('datasources.repository');
                    $dataSource = $dataSourcesRepository->getDataSourceById($dataSourceId, $language);
                    $dataSourceKeys = $dataSource->getKeys();
                    $dataSourceModified = false;
                    foreach (explode(',', $value) as $singleValue) {
                        if (!in_array($singleValue, $dataSourceKeys)) {
                            $dataSource->addKey($singleValue, true);
                            $dataSourceModified = true;
                        }
                    }
                    if ($dataSourceModified) {
                        $dataSourcesRepository->save($dataSource, $this->getUser()->getId());
                    }
                }

                $metaSetItem->$key = $value ? $value : '';
            }

            $metaSetItem->save();

            if ($isMaster) {
                $slaveLanguages = $container->getParameter('frontend.languages.available');
                unset($slaveLanguages[array_search($language, $slaveLanguages)]);

                foreach ($slaveLanguages as $slaveLanguage) {
                    $identifier = new Makeweb_Elements_Element_Version_MetaSet_Identifier($elementVersion, $slaveLanguage);
                    /* @var $metaSetItem Media_MetaSets_Item */
                    $metaSetItem = Media_MetaSets_Item_Peer::get($metaSetId, $identifier);

                    foreach ($data['meta'] as $key => $value) {
                        if (!$metaSetArray[$key]['synchronized'] && $metaSetItem->$key) {
                            continue;
                        } elseif (!$metaSetArray[$key]['synchronized'] && !$metaSetItem->$key) {
                            $metaSetItem->$key = '';
                        } else {
                            $metaSetItem->$key = $value ? $value : '';
                        }
                    }

                    $metaSetItem->save();
                }
            }
        }

        // save context

        if (isset($data['context'])) {
            $db->delete($db->prefix . 'tree_context', array('tid = ?' => $tid));

            $insertData = array(
                'tid' => $tid
            );

            foreach ($data['context'] as $country) {
                $insertData['context'] = $country;

                $db->insert($db->prefix . 'element_tree_context', $insertData);
            }
        }

        $event = new SaveNodeDataEvent($node, $language, $data);
        $this->dispatcher->dispatch(ElementEvents::SAVE_NODE_DATA, $event);
    }

    /**
     * @param ElementVersion       $elementVersion
     * @param ElementtypeStructure $elementtypeStructure
     * @param array                $values
     *
     * @return ElementStructure
     */
    private function createStructure($elementVersion, $elementtypeStructure, array $values)
    {
        $this->structures[null] = $rootElementStructure = new ElementStructure();

        foreach ($values as $key => $value) {
            $parts = explode('__', $key);
            $fixed = $parts[0];
            $repeatableGroup = null;
            if (isset($parts[1])) {
                $repeatableGroup = $parts[1];
            }

            if (preg_match('/^field-([-a-f0-9]{36})-id-([0-9]+)$/', $fixed, $match)) {
                // existing root value
                $dsId = $match[1];
                $id = $match[2];
                $node = $elementtypeStructure->getNode($dsId);
                $elementStructureValue = new ElementStructureValue($dsId, $node->getName(), $node->getType(), $value);
                $elementStructure = $this->findGroup($repeatableGroup);
                $elementStructure->setValue($elementStructureValue);
            } elseif (preg_match('/^field-([-a-f0-9]{36})-new-([0-9]+)$/', $fixed, $match)) {
                // new root value
                $dsId = $match[1];
                $id = $match[2];
                $node = $elementtypeStructure->getNode($dsId);
                $elementStructureValue = new ElementStructureValue($dsId, $node->getName(), $node->getType(), $value);
                $elementStructure = $this->findGroup($repeatableGroup);
                $elementStructure->setValue($elementStructureValue);
            } elseif (preg_match('/^group-([-a-f0-9]{36})-id-([0-9]+)$/', $fixed, $match)) {
                // existing repeatable group
                $parent = $this->findGroup($repeatableGroup);
                $dsId = $match[1];
                $id = $match[2];
                $node = $elementtypeStructure->getNode($dsId);
                $this->structures[$id] = $elementStructure = new ElementStructure();
                $elementStructure
                    ->setElementVersion($elementVersion)
                    ->setDsId($dsId)
                    ->setId($id)
                    ->setParentDsId($parent->getDsId())
                    ->setParentName($parent->getName())
                    ->setName($node->getName());
                $parent->addStructure($elementStructure);
            } elseif (preg_match('/^group-([-a-f0-9]{36})-new-([0-9]+)$/', $fixed, $match)) {
                // new repeatable group
                $parent = $this->findGroup($repeatableGroup);
                $dsId = $match[1];
                $id = $match[2];
                $this->structures[$id] = $elementStructure = new ElementStructure();
                $elementStructure
                    ->setElementVersion($elementVersion)
                    ->setDsId($dsId)
                    ->setId($id)
                    ->setParentDsId($parent->getDsId())
                    ->setParentName($parent->getName())
                    ->setName('xxx');
                $parent->addStructure($elementStructure);
            }
        }

        return $rootElementStructure;
    }

    /**
     * @param string $identifier
     *
     * @return ElementStructure
     */
    private function findGroup($identifier)
    {
        if (preg_match('/^group-([-a-f0-9]{36})-id-([0-9]+)$/', $identifier, $match)) {
            // existing repeatable group
            $dsId = $match[1];
            $id = $match[2];
            return $this->structures[$id];
        } elseif (preg_match('/^group-([-a-f0-9]{36})-new-([0-9]+)$/', $identifier, $match)) {
            // new repeatable group
            $dsId = $match[1];
            $id = $match[2];
            return $this->structures[$id];
        } else {
            return $this->structures[null];
        }
    }
}
