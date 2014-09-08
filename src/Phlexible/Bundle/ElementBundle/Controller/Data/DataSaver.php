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
use Phlexible\Bundle\ElementBundle\ElementVersion\FieldMapper;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\ElementBundle\Event\SaveElementEvent;
use Phlexible\Bundle\ElementBundle\Event\SaveNodeDataEvent;
use Phlexible\Bundle\ElementBundle\Event\SaveTeaserDataEvent;
use Phlexible\Bundle\ElementBundle\Meta\ElementMetaDataManager;
use Phlexible\Bundle\ElementBundle\Meta\ElementMetaSetResolver;
use Phlexible\Bundle\ElementBundle\Model\ElementStructure;
use Phlexible\Bundle\ElementBundle\Model\ElementStructureValue;
use Phlexible\Bundle\ElementtypeBundle\Field\FieldRegistry;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructure;
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
     * @var FieldMapper
     */
    private $fieldMapper;

    /**
     * @var TreeManager
     */
    private $treeManager;

    /**
     * @var TeaserManagerInterface
     */
    private $teaserManager;

    /**
     * @var ElementMetaSetResolver
     */
    private $elementMetaSetResolver;

    /**
     * @var ElementMetaDataManager
     */
    private $elementMetaDataManager;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var array
     */
    private $availableLanguages;

    /**
     * @var ElementStructure[]
     */
    private $structures = array();

    /**
     * @param ElementService           $elementService
     * @param FieldMapper              $fieldMapper
     * @param TreeManager              $treeManager
     * @param TeaserManagerInterface   $teaserManager
     * @param EventDispatcherInterface $dispatcher
     * @param string                   $availableLanguages
     */
    public function __construct(
        ElementService $elementService,
        FieldMapper $fieldMapper,
        TreeManager $treeManager,
        TeaserManagerInterface $teaserManager,
        EventDispatcherInterface $dispatcher,
        $availableLanguages)
    {
        $this->elementService = $elementService;
        $this->fieldMapper = $fieldMapper;
        $this->treeManager = $treeManager;
        $this->teaserManager = $teaserManager;
        $this->dispatcher = $dispatcher;
        $this->availableLanguages = explode(',', $availableLanguages);
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
        $isPublish = $request->get('publish');
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
            $teaser = $this->teaserManager->find($teaserId);
        }

        $elementComment = null;
        if (!empty($data['comment'])) {
            $elementComment = $data['comment'];
        }

        $elementVersion = clone $oldElementVersion;
        $elementVersion
            ->setId(null)
            ->setElement($element)
            ->setVersion($elementVersion->getVersion() + 1)
            ->setCreateUserId($user->getId())
            ->setCreatedAt(new \DateTime())
            ->setComment($elementComment)
            ->setTriggerLanguage($language);

        $element->setLatestVersion($elementVersion->getVersion());

        $elementStructure = $this->createStructure($elementVersion, $elementtypeStructure, $values, $language);

        $this->fieldMapper->apply($elementVersion, $elementStructure, array($language));

        $event = new SaveElementEvent($element, $language, $oldVersion);
        $this->dispatcher->dispatch(ElementEvents::BEFORE_SAVE_ELEMENT, $event);

        $this->elementService->updateElement($element, false);
        $this->elementService->updateElementVersion($elementVersion);
        $this->elementService->updateElementStructure($elementStructure);

        if ($teaser) {
            $this->saveTeaserData($teaser, $language, $data);
        } else {
            $this->saveNodeData($node, $language, $data);
        }

        // TODO: available languages
        $this->saveMeta($elementVersion, $language, $isMaster, array('de'));

        $event = new SaveElementEvent($element, $language, $oldVersion);
        $this->dispatcher->dispatch(ElementEvents::SAVE_ELEMENT, $event);

        $publishSlaves = array();
        if ($isPublish) {
            $publishSlaves = $this->checkPublishSlaves($elementVersion, $node, $teaser, $language);
            if ($teaser) {
                $this->publishTeaser($elementVersion, $teaser, $language, $user->getId(), $elementComment, $publishSlaves);
            } else {
                $this->publishTreeNode($elementVersion, $node, $language, $user->getId(), $elementComment, $publishSlaves);
            }
        }

        return array($elementVersion, $node, $teaser, $publishSlaves);
    }

    /**
     * @param Teaser $teaser
     * @param string $language
     * @param array  $data
     */
    private function saveTeaserData(Teaser $teaser, $language, array $data)
    {
        if (!empty($data['configuration'])) {
            if (!empty($data['configuration']['controller'])) {
                $teaser->setController($data['configuration']['controller']);
            } else {
                $teaser->setController(null);
            }
            if (!empty($data['configuration']['template'])) {
                $teaser->setTemplate($data['configuration']['template']);
            } else {
                $teaser->setTemplate(null);
            }
            if (!empty($data['configuration']['noCache'])) {
                $teaser->setAttribute('noCache', true);
            } else {
                $teaser->removeAttribute('noCache');
            }
            if (!empty($data['configuration']['cachePrivate'])) {
                $teaser->setAttribute('cachePrivate', true);
            } else {
                $teaser->removeAttribute('cachePrivate');
            }
            if (!empty($data['configuration']['cacheMaxAge'])) {
                $teaser->setAttribute('cacheMaxAge', (int) $data['configuration']['cacheMaxAge']);
            } else {
                $teaser->removeAttribute('cacheMaxAge');
            }
            if (!empty($data['configuration']['cacheSharedMaxAge'])) {
                $teaser->setAttribute('cacheSharedMaxAge', (int) $data['configuration']['cacheSharedMaxAge']);
            } else {
                $teaser->removeAttribute('cacheSharedMaxAge');
            }

            $this->teaserManager->updateTeaser($teaser);
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
        // save configuration

        if (!empty($data['configuration'])) {
            if (!empty($data['configuration']['navigation'])) {
                $node->setInNavigation(true);
            } else {
                $node->setInNavigation(false);
            }
            if (!empty($data['configuration']['needAuthentication'])) {
                $node->setNeedAuthentication(true);
            } else {
                $node->setNeedAuthentication(false);
            }
            if (!empty($data['configuration']['https'])) {
                $node->setAttribute('https', true);
            } else {
                $node->removeAttribute('https');
            }
            $routes = $node->getRoutes();
            if (!empty($data['configuration']['route'])) {
                $routes[$language] = $data['configuration']['route'];
            } else {
                if (!count($routes)) {
                    $node->setRoutes(null);
                }
            }
            if (!empty($data['configuration']['controller'])) {
                $node->setController($data['configuration']['controller']);
            } else {
                $node->setController(null);
            }
            if (!empty($data['configuration']['template'])) {
                $node->setTemplate($data['configuration']['template']);
            } else {
                $node->setTemplate(null);
            }
            if (!empty($data['configuration']['robotsNoIndex'])) {
                $node->setAttribute('robotsNoIndex', true);
            } else {
                $node->removeAttribute('robotsNoIndex');
            }
            if (!empty($data['configuration']['robotsNoFollow'])) {
                $node->setAttribute('robotsNoFollow', true);
            } else {
                $node->removeAttribute('robotsNoFollow');
            }
            if (!empty($data['configuration']['searchNoIndex'])) {
                $node->setAttribute('searchNoIndex', true);
            } else {
                $node->removeAttribute('searchNoIndex');
            }
            if (!empty($data['configuration']['noCache'])) {
                $node->setAttribute('noCache', true);
            } else {
                $node->removeAttribute('noCache');
            }
            if (!empty($data['configuration']['cachePrivate'])) {
                $node->setAttribute('cachePrivate', true);
            } else {
                $node->removeAttribute('cachePrivate');
            }
            if (!empty($data['configuration']['cacheMaxAge'])) {
                $node->setAttribute('cacheMaxAge', (int) $data['configuration']['cacheMaxAge']);
            } else {
                $node->removeAttribute('cacheMaxAge');
            }
            if (!empty($data['configuration']['cacheSharedMaxAge'])) {
                $node->setAttribute('cacheSharedMaxAge', (int) $data['configuration']['cacheSharedMaxAge']);
            } else {
                $node->removeAttribute('cacheSharedMaxAge');
            }

            $node->getTree()->updateNode($node);
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
     * @param ElementVersion $elementVersion
     * @param string         $language
     * @param bool           $isMaster
     * @param array          $availableLanguages
     */
    private function saveMeta(ElementVersion $elementVersion, $language, $isMaster, $availableLanguages)
    {
        // save meta
        // TODO: repair save meta

        if (empty($data['meta'])) {
            return;
        }

        $metaSet = $this->elementMetaSetResolver->resolve($elementVersion);

        if (!$metaSet) {
            return;
        }

        $metaData = $this->elementMetaDataManager->findByMetaSetAndElementVersion($metaSet, $elementVersion);

        /*
        $slaveLanguages = array();
        if ($isMaster) {
            $slaveLanguages = $availableLanguages;
            unset($slaveLanguages[array_search($language, $slaveLanguages)]);
        }
        */

        // TODO: copy old values

        foreach ($data['meta'] as $field => $value) {
            if (!$metaSet->hasField($field)) {
                unset($data['meta'][$field]);
                continue;
            }

            // TODO: repair suggest
            /*
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
            */

            // TODO: master check?
            if ($metaSet->getField($field)->isSynchronized()) {
                foreach ($availableLanguages as $currentLanguage) {
                    $metaData->set($field, $value, $currentLanguage);
                }
            } else {
                $metaData->set($field, $value, $language);
            }
        }

        $this->elementMetaDataManager->updateMetaData($metaData);
    }

    /**
     * @param ElementVersion       $elementVersion
     * @param ElementtypeStructure $elementtypeStructure
     * @param array                $values
     * @param string               $language
     *
     * @return ElementStructure
     */
    private function createStructure($elementVersion, $elementtypeStructure, array $values, $language)
    {
        $rootElementStructure = new ElementStructure();
        $rootElementStructure->setElementVersion($elementVersion);

        $this->structures[null] = $rootElementStructure;

        foreach ($values as $key => $value) {
            $parts = explode('__', $key);
            $identifier = $parts[0];
            $repeatableIdentifier = null;
            if (isset($parts[1])) {
                $repeatableIdentifier = $parts[1];
            }

            if (preg_match('/^field-([-a-f0-9]{36})-id-([0-9]+)$/', $identifier, $match)) {
                // existing root value
                $dsId = $match[1];
                $id = $match[2];
                $node = $elementtypeStructure->getNode($dsId);
                $options = null;
                $elementStructureValue = new ElementStructureValue($id, $dsId, $language, $node->getType(), $node->getName(), $value, $options);
                $elementStructure = $this->structures[$repeatableIdentifier];
                $elementStructure->setValue($elementStructureValue);
            } elseif (preg_match('/^field-([-a-f0-9]{36})-new-([0-9]+)$/', $identifier, $match)) {
                // new root value
                $dsId = $match[1];
                $foundId = $match[2];
                $id = $this->elementService->getElementStructureManager()->getNextStructureValueId();
                $node = $elementtypeStructure->getNode($dsId);
                $options = null;
                $elementStructureValue = new ElementStructureValue($id, $dsId, $language, $node->getType(), $node->getName(), $value, $options);
                $elementStructure = $this->structures[$repeatableIdentifier];
                $elementStructure->setValue($elementStructureValue);
            } elseif (preg_match('/^group-([-a-f0-9]{36})-id-([0-9]+)$/', $identifier, $match)) {
                // existing repeatable group
                $parent = $this->structures[$repeatableIdentifier];
                $dsId = $match[1];
                $id = $match[2];
                $node = $elementtypeStructure->getNode($dsId);
                $this->structures[$identifier] = $elementStructure = new ElementStructure();
                $elementStructure
                    ->setElementVersion($elementVersion)
                    ->setId($id)
                    ->setDsId($dsId)
                    #->setRepeatableId($parent->getId())
                    #->setRepeatableDsId($parent->getDsId())
                    ->setParentName($parent->getName())
                    ->setName($node->getName());
                $parent->addStructure($elementStructure);
            } elseif (preg_match('/^group-([-a-f0-9]{36})-new-([0-9]+)$/', $identifier, $match)) {
                // new repeatable group
                $parent = $this->structures[$repeatableIdentifier];
                $dsId = $match[1];
                $foundId = $match[2];
                $id = $this->elementService->getElementStructureManager()->getNextStructureId();
                $node = $elementtypeStructure->getNode($dsId);
                $this->structures[$identifier] = $elementStructure = new ElementStructure();
                $elementStructure
                    ->setElementVersion($elementVersion)
                    ->setId($id)
                    ->setDsId($dsId)
                    #->setRepeatableId($parent->getId())
                    #->setRepeatableDsId($parent->getDsId())
                    ->setParentName($parent->getName())
                    ->setName($node->getName());
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
        return $this->structures[$identifier];
    }

    /**
     * @param ElementVersion    $elementVersion
     * @param TreeNodeInterface $node
     * @param Teaser            $teaser
     * @param string            $language
     *
     * @return array
     */
    private function checkPublishSlaves(ElementVersion $elementVersion, TreeNodeInterface $node, Teaser $teaser = null, $language)
    {
        $publishSlaves = array('elements' => array(), 'languages' => array());

        if ($elementVersion->getElement()->getMasterLanguage() !== $language) {
            return $publishSlaves;
        }

        foreach ($this->availableLanguages as $slaveLanguage) {
            if ($language === $slaveLanguage) {
                continue;
            }

            if ($teaser) {
                if ($this->teaserManager->isPublished($teaser, $slaveLanguage)) {
                    if (!$this->teaserManager->isAsync($teaser, $slaveLanguage)) {
                        $publishSlaves['languages'][] = $slaveLanguage;
                    } else {
                        $publishSlaves['elements'][] = array($teaser->getId(), $slaveLanguage, $elementVersion->getVersion(), 'async', 1);
                    }
                }
                // TODO: needed?
                /*
                } else {
                    if ($this->container->getParameter(
                        'phlexible_element.publish.cross_language_publish_offline'
                    )
                    ) {
                        $publishSlaves[] = array($teaser->getId(), $slaveLanguage, 0, '', 0);
                    }
                */
            } else {
                if ($node->getTree()->isPublished($node, $slaveLanguage)) {
                    if (!$node->getTree()->isAsync($node, $slaveLanguage)) {
                        $publishSlaves['languages'][] = $slaveLanguage;
                    } else {
                        $publishSlaves['elements'][] = array($node->getId(), $slaveLanguage, 0, 'async', 1);
                    }
                }
                // TODO: needed?
                /*
                } else {
                    if ($this->container->getParameter('phlexible_element.publish.cross_language_publish_offline')) {
                        $publishSlaves[] = array($node->getId(), $slaveLanguage, $newVersion, '', 0);
                    }
                */
            }
        }

        return $publishSlaves;
    }

    /**
     * @param ElementVersion $elementVersion
     * @param Teaser         $teaser
     * @param string         $language
     * @param string         $userId
     * @param string|null    $comment
     * @param array          $publishSlaves
     */
    private function publishTeaser(ElementVersion $elementVersion, Teaser $teaser = null, $language, $userId, $comment = null, array $publishSlaves = array())
    {
        $this->teaserManager->publishTeaser(
            $teaser,
            $elementVersion->getVersion(),
            $language,
            $userId,
            $comment
        );

        if (count($publishSlaves['languages'])) {
            foreach ($publishSlaves['languages'] as $slaveLanguage) {
                // publish slave node
                $this->teaserManager->publishTeaser(
                    $teaser,
                    $elementVersion->getVersion(),
                    $slaveLanguage,
                    $userId,
                    $comment
                );
            }
        }
    }

    /**
     * @param ElementVersion    $elementVersion
     * @param TreeNodeInterface $treeNode
     * @param string            $language
     * @param string            $userId
     * @param string|null       $comment
     * @param array             $publishSlaves
     */
    private function publishTreeNode(ElementVersion $elementVersion, TreeNodeInterface $treeNode, $language, $userId, $comment = null, array $publishSlaves = array())
    {
        $tree = $treeNode->getTree();

        // publish node
        $tree->publish(
            $treeNode,
            $elementVersion->getVersion(),
            $language,
            $userId,
            $comment
        );

        if (!empty($publishSlaves['languages'])) {
            foreach ($publishSlaves['languages'] as $slaveLanguage) {
                // publish slave node
                $tree->publish(
                    $treeNode,
                    $elementVersion->getVersion(),
                    $slaveLanguage,
                    $userId,
                    $comment
                );

                // TODO: gnarf
                // workaround to fix missing catch results for non master language elements
                /*
                Makeweb_Elements_Element_History::insert(
                    Makeweb_Elements_Element_History::ACTION_SAVE,
                    $eid,
                    $newVersion,
                    $slaveLanguage
                );
                */
            }
        }
    }
}
