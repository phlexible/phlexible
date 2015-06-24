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
use Phlexible\Bundle\ElementBundle\Exception\InvalidArgumentException;
use Phlexible\Bundle\ElementBundle\Meta\ElementMetaDataManager;
use Phlexible\Bundle\ElementBundle\Meta\ElementMetaSetResolver;
use Phlexible\Bundle\ElementBundle\Model\ElementStructure;
use Phlexible\Bundle\ElementBundle\Model\ElementStructureValue;
use Phlexible\Bundle\ElementtypeBundle\Field\FieldRegistry;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructure;
use Phlexible\Bundle\GuiBundle\Util\Uuid;
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
     * @var FieldRegistry
     */
    private $fieldRegistry;

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
    private $eventDispatcher;

    /**
     * @var array
     */
    private $availableLanguages;

    /**
     * @var ElementStructure[]
     */
    private $structures = [];

    /**
     * @param ElementService           $elementService
     * @param FieldRegistry            $fieldRegistry
     * @param TreeManager              $treeManager
     * @param TeaserManagerInterface   $teaserManager
     * @param ElementMetaSetResolver   $elementMetaSetResolver
     * @param ElementMetaDataManager   $elementMetaDataManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param string                   $availableLanguages
     */
    public function __construct(
        ElementService $elementService,
        FieldRegistry $fieldRegistry,
        TreeManager $treeManager,
        TeaserManagerInterface $teaserManager,
        ElementMetaSetResolver $elementMetaSetResolver,
        ElementMetaDataManager $elementMetaDataManager,
        EventDispatcherInterface $eventDispatcher,
        $availableLanguages)
    {
        $this->elementService = $elementService;
        $this->fieldRegistry = $fieldRegistry;
        $this->treeManager = $treeManager;
        $this->teaserManager = $teaserManager;
        $this->elementMetaSetResolver = $elementMetaSetResolver;
        $this->elementMetaDataManager = $elementMetaDataManager;
        $this->eventDispatcher = $eventDispatcher;
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
        $isPublish = $request->get('isPublish');
        $values = $request->get('values');
        $publishComment = $request->get('publishComment');

        if ($values) {
            $values = json_decode($values, true);
        }

        $element = $this->elementService->findElement($eid);
        $elementtype = $this->elementService->findElementtype($element);
        $elementtypeStructure = $elementtype->getStructure();

        $oldElementVersion = $this->elementService->findLatestElementVersion($element);
        $oldElementStructure = $this->elementService->findElementStructure($oldElementVersion, 'de');
        $oldVersion = $oldElementVersion->getVersion();
        $isMaster = $element->getMasterLanguage() === $language;

        $tree = $this->treeManager->getByNodeId($tid);
        $node = $tree->get($tid);

        $teaser = null;
        if ($teaserId) {
            $teaser = $this->teaserManager->find($teaserId);
        }

        $comment = null;
        if ($request->get('comment')) {
            $comment = $request->get('comment');
        }

        if ($language === $element->getMasterLanguage()) {
            $elementStructure = new ElementStructure();
            if ($oldElementStructure->getDataId()) {
                $elementStructure
                    ->setDataId($oldElementStructure->getDataId())
                    ->setDsId($oldElementStructure->getDsId())
                    ->setType('root')
                    ->setName($oldElementStructure->getName());
            } else {
                $elementStructure
                    ->setDataId(Uuid::generate())
                    ->setDsId($elementtypeStructure->getRootDsId())
                    ->setType('root')
                    ->setName($elementtypeStructure->getRootNode()->getName());
            }
            $map = $this->applyStructure($elementStructure, $elementtypeStructure, $values, $language, $oldElementStructure);
            $this->applyOldValues($elementStructure, $oldElementStructure, $language);
            $this->applyValues($elementStructure, $elementtypeStructure, $values, $language, $map);
        } else {
            $elementStructure = clone $oldElementStructure;
            $this->applyValues($elementStructure, $elementtypeStructure, $values, $language);
        }

        $elementVersion = $this->elementService->createElementVersion($element, $elementStructure, $language, $user->getId(), $comment);

        if ($teaser) {
            $this->saveTeaserData($teaser, $language, $request);
        } else {
            $this->saveNodeData($node, $language, $request);
        }

        // TODO: available languages
        $this->saveMeta($elementVersion, $language, $isMaster, ['de'], $request);

        $event = new SaveElementEvent($element, $language, $oldVersion);
        $this->eventDispatcher->dispatch(ElementEvents::SAVE_ELEMENT, $event);

        $publishSlaves = [];
        if ($isPublish) {
            $publishSlaves = $this->checkPublishSlaves($elementVersion, $node, $teaser, $language);
            if ($teaser) {
                $this->publishTeaser($elementVersion, $teaser, $language, $user->getId(), $publishComment, $publishSlaves);
            } else {
                $this->publishTreeNode($elementVersion, $node, $language, $user->getId(), $publishComment, $publishSlaves);
            }
        }

        return [$elementVersion, $node, $teaser, $publishSlaves];
    }

    /**
     * @param Teaser  $teaser
     * @param string  $language
     * @param Request $request
     */
    private function saveTeaserData(Teaser $teaser, $language, Request $request)
    {
        if ($request->get('configuration')) {
            $configuration = json_decode($request->get('configuration'), true);

            if (!empty($configuration['controller'])) {
                $teaser->setController($configuration['controller']);
            } else {
                $teaser->setController(null);
            }
            if (!empty($configuration['template'])) {
                $teaser->setTemplate($configuration['template']);
            } else {
                $teaser->setTemplate(null);
            }
            if (!empty($configuration['noCache'])) {
                $teaser->setAttribute('noCache', true);
            } else {
                $teaser->removeAttribute('noCache');
            }
            if (!empty($configuration['cachePrivate'])) {
                $teaser->setAttribute('cachePrivate', true);
            } else {
                $teaser->removeAttribute('cachePrivate');
            }
            if (!empty($configuration['cacheMaxAge'])) {
                $teaser->setAttribute('cacheMaxAge', (int) $configuration['cacheMaxAge']);
            } else {
                $teaser->removeAttribute('cacheMaxAge');
            }
            if (!empty($configuration['cacheSharedMaxAge'])) {
                $teaser->setAttribute('cacheSharedMaxAge', (int) $configuration['cacheSharedMaxAge']);
            } else {
                $teaser->removeAttribute('cacheSharedMaxAge');
            }

            $this->teaserManager->updateTeaser($teaser);
        }

        $event = new SaveTeaserDataEvent($teaser, $language, $request);
        $this->eventDispatcher->dispatch(ElementEvents::SAVE_TEASER_DATA, $event);
    }

    /**
     * @param TreeNodeInterface $node
     * @param string            $language
     * @param Request           $request
     */
    private function saveNodeData(TreeNodeInterface $node, $language, Request $request)
    {
        // save configuration

        if ($request->get('configuration')) {
            $configuration = json_decode($request->get('configuration'), true);

            if (!empty($configuration['navigation'])) {
                $node->setInNavigation(true);
            } else {
                $node->setInNavigation(false);
            }
            if (!empty($configuration['template'])) {
                $node->setTemplate($configuration['template']);
            } else {
                $node->setTemplate(null);
            }
            if (!empty($configuration['robotsNoIndex'])) {
                $node->setAttribute('robotsNoIndex', true);
            } else {
                $node->removeAttribute('robotsNoIndex');
            }
            if (!empty($configuration['robotsNoFollow'])) {
                $node->setAttribute('robotsNoFollow', true);
            } else {
                $node->removeAttribute('robotsNoFollow');
            }
            if (!empty($configuration['searchNoIndex'])) {
                $node->setAttribute('searchNoIndex', true);
            } else {
                $node->removeAttribute('searchNoIndex');
            }
        }

        if ($request->get('security')) {
            $security = json_decode($request->get('security'), true);

            $node->setAttribute('security', $security);

            if (!empty($security['authentication_required'])) {
                $node->setAttribute('authenticationRequired', true);
            } else {
                $node->removeAttribute('authenticationRequired');
            }
            if (!empty($security['roles'])) {
                $node->setAttribute('roles', $security['roles']);
            } else {
                $node->removeAttribute('roles');
            }
            if (!empty($security['check_acl'])) {
                $node->setAttribute('checkAcl', true);
            } else {
                $node->removeAttribute('checkAcl');
            }
            if (!empty($security['expression'])) {
                $node->setAttribute('expression', $security['expression']);
            } else {
                $node->removeAttribute('expression');
            }
        } else {
            $node->removeAttribute('security');
        }

        if ($request->get('cache')) {
            $cache = json_decode($request->get('cache'), true);

            $node->setAttribute('cache', $cache);

            if (!empty($cache['expires'])) {
                $node->setAttribute('expires', $cache['expires']);
            } else {
                $node->removeAttribute('expires');
            }
            if (!empty($cache['public'])) {
                $node->setAttribute('public', true);
            } else {
                $node->removeAttribute('public');
            }
            if (!empty($cache['maxage'])) {
                $node->setAttribute('maxage', $cache['maxage']);
            } else {
                $node->removeAttribute('maxage');
            }
            if (!empty($cache['smaxage'])) {
                $node->setAttribute('smaxage', $cache['smaxage']);
            } else {
                $node->removeAttribute('smaxage');
            }
            if (!empty($cache['vary'])) {
                $node->setAttribute('vary', $cache['vary']);
            } else {
                $node->removeAttribute('vary');
            }
        } else {
            $node->removeAttribute('cache');
        }

        if ($request->get('routing')) {
            $routing = json_decode($request->get('routing'), true);

            $node->setAttribute('routing', $routing);

            /*
            if (!empty($routing['name'])) {
                $node->setAttribute('name', $routing['name']);
            } else {
                $node->removeAttribute('name');
            }
            if (!empty($routing['path'])) {
                $node->setAttribute('path', $routing['path']);
            } else {
                $node->removeAttribute('path');
            }
            if (!empty($routing['defaults'])) {
                $node->setAttribute('defaults', $routing['defaults']);
            } else {
                $node->removeAttribute('defaults');
            }
            if (!empty($routing['methods'])) {
                $node->setAttribute('methods', $routing['methods']);
            } else {
                $node->removeAttribute('methods');
            }
            if (!empty($routing['schemes'])) {
                $node->setAttribute('schemes', $routing['schemes']);
            } else {
                $node->removeAttribute('schemes');
            }
            if (!empty($routing['controller'])) {
                $node->setAttribute('controller', $routing['controller']);
            } else {
                $node->removeAttribute('controller');
            }
            */
        } else {
            $node->removeAttribute('routing');
        }

        $event = new SaveNodeDataEvent($node, $language, $request);
        $this->eventDispatcher->dispatch(ElementEvents::SAVE_NODE_DATA, $event);

        $node->getTree()->updateNode($node);
    }

    /**
     * @param ElementVersion $elementVersion
     * @param string         $language
     * @param bool           $isMaster
     * @param array          $availableLanguages
     * @param Request        $request
     */
    private function saveMeta(ElementVersion $elementVersion, $language, $isMaster, array $availableLanguages, Request $request)
    {
        // save meta
        // TODO: repair save meta

        if (!$request->get('meta')) {
            return;
        }

        $metaSet = $this->elementMetaSetResolver->resolve($elementVersion);

        if (!$metaSet) {
            return;
        }

        $metaData = $this->elementMetaDataManager->findByMetaSetAndElementVersion($metaSet, $elementVersion);

        if (!$metaData) {
            $metaData = $this->elementMetaDataManager->createElementMetaData($metaSet, $elementVersion);
        }

        /*
        $slaveLanguages = array();
        if ($isMaster) {
            $slaveLanguages = $availableLanguages;
            unset($slaveLanguages[array_search($language, $slaveLanguages)]);
        }
        */

        // TODO: copy old values

        $meta = $request->get('meta');

        if ($meta) {
            $meta = json_decode($meta);
        }

        foreach ($meta as $field => $value) {
            if (!$metaSet->hasField($field)) {
                unset($meta[$field]);
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
     * @param ElementStructure     $rootElementStructure
     * @param ElementtypeStructure $elementtypeStructure
     * @param array                $values
     *
     * @return ElementStructure
     */
    private function applyStructure(ElementStructure $rootElementStructure, ElementtypeStructure $elementtypeStructure, array $values)
    {
        $this->structures[null] = $rootElementStructure;
        $map = [null => $rootElementStructure->getDataId()];

        foreach ($values as $key => $value) {
            $parts = explode('__', $key);
            $identifier = $parts[0];
            $repeatableIdentifier = null;
            if (isset($parts[1])) {
                $repeatableIdentifier = $parts[1];
            }

            if (preg_match('/^group-([-a-f0-9]{36})-id-([a-fA-F0-9-]+)$/', $identifier, $match)) {
                // existing repeatable group
                $parent = $this->structures[$repeatableIdentifier];
                $dsId = $match[1];
                $dataId = $match[2];
                $node = $elementtypeStructure->getNode($dsId);
                $map[$identifier] = $dataId;
                $this->structures[$identifier] = $elementStructure = new ElementStructure();
                $elementStructure
                    ->setDataId($dataId)
                    ->setDsId($dsId)
                    ->setParentName($parent->getName())
                    ->setName($node->getName());
                $parent->addStructure($elementStructure);
            } elseif (preg_match('/^group-([-a-f0-9]{36})-new-.+$/', $identifier, $match)) {
                // new repeatable group
                $parent = $this->structures[$repeatableIdentifier];
                $dsId = $match[1];
                $dataId = Uuid::generate();
                $node = $elementtypeStructure->getNode($dsId);
                $map[$identifier] = $dataId;
                $this->structures[$identifier] = $elementStructure = new ElementStructure();
                $elementStructure
                    ->setDataId($dataId)
                    ->setDsId($dsId)
                    ->setParentName($parent->getName())
                    ->setName($node->getName());
                $parent->addStructure($elementStructure);
            }
        }

        return $map;
    }

    /**
     * @param ElementStructure $rootElementStructure
     * @param ElementStructure $oldRootElementStructure
     * @param string           $skipLanguage
     */
    private function applyOldValues(ElementStructure $rootElementStructure, ElementStructure $oldRootElementStructure, $skipLanguage)
    {
        foreach ($oldRootElementStructure->getValues() as $value) {
            if ($value->getLanguage() === $skipLanguage) {
                continue;
            }
            $rootElementStructure->setValue($value);
        }

        $rii = new \RecursiveIteratorIterator($rootElementStructure->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($rii as $structure) {
            /* @var $structure ElementStructure */
            $oldStructure = $this->findStructureByDataId($oldRootElementStructure, $structure->getDataId());
            if (!$oldStructure) {
                continue;
            }
            foreach ($oldStructure->getValues() as $value) {
                if ($value->getLanguage() === $skipLanguage) {
                    continue;
                }
                $structure->setValue($value);
            }
        }
    }

    /**
     * @param ElementStructure     $rootElementStructure
     * @param ElementtypeStructure $elementtypeStructure
     * @param array                $values
     * @param string               $language
     * @param array                $map
     *
     * @throws InvalidArgumentException
     * @return ElementStructure
     */
    private function applyValues(ElementStructure $rootElementStructure, ElementtypeStructure $elementtypeStructure, array $values, $language, array $map = null)
    {
        $rootElementStructure->removeLanguage($language);

        foreach ($values as $key => $value) {
            $parts = explode('__', $key);
            $identifier = $parts[0];
            $repeatableIdentifier = null;
            if (isset($parts[1])) {
                $repeatableIdentifier = $parts[1];
            }

            if (preg_match('/^field-([-a-f0-9]{36})-id-([0-9]+)$/', $identifier, $match)) {
                // existing value
                $dsId = $match[1];
                $node = $elementtypeStructure->getNode($dsId);
                $options = null;
                $field = $this->fieldRegistry->getField($node->getType());
                $value = $field->fromRaw($value);
                $elementStructureValue = new ElementStructureValue(null, $dsId, $language, $node->getType(), $field->getDataType(), $node->getName(), $value, $options);
                if ($map) {
                    $mapId = $map[$repeatableIdentifier];
                } else {
                    $mapId = $rootElementStructure->getDataId();
                    if ($repeatableIdentifier) {
                        $mapId = mb_substr($repeatableIdentifier, -36, null, 'UTF-8');
                    }
                }
                $elementStructure = $this->findStructureByDataId($rootElementStructure, $mapId);
                if (!$elementStructure) {
                    throw new InvalidArgumentException("Element structure $mapId not found. Repeatable identifier: $repeatableIdentifier. Map: " . print_r($map, true));
                }
                $elementStructure->setValue($elementStructureValue);
            } elseif (preg_match('/^field-([-a-f0-9]{36})-new-.+$/', $identifier, $match)) {
                // new value
                $dsId = $match[1];
                $node = $elementtypeStructure->getNode($dsId);
                $field = $this->fieldRegistry->getField($node->getType());
                $value = $field->fromRaw($value);
                $options = null;
                $elementStructureValue = new ElementStructureValue(null, $dsId, $language, $node->getType(), $field->getDataType(), $node->getName(), $value, $options);
                if ($map) {
                    $mapId = $map[$repeatableIdentifier];
                } else {
                    $mapId = $rootElementStructure->getDataId();
                    if ($repeatableIdentifier) {
                        $mapId = mb_substr($repeatableIdentifier, -36, null, 'UTF-8');
                    }
                }
                $elementStructure = $this->findStructureByDataId($rootElementStructure, $mapId);
                if (!$elementStructure) {
                    throw new InvalidArgumentException("Element structure $mapId not found. Repeatable identifier: $repeatableIdentifier. Map: " . print_r($map, true));
                }
                $elementStructure->setValue($elementStructureValue);
            }
        }

        return $rootElementStructure;
    }

    /**
     * @param ElementStructure $structure
     * @param string           $dataId
     *
     * @return ElementStructure|null
     */
    private function findStructureByDataId(ElementStructure $structure, $dataId)
    {
        if ($structure->getDataId() === $dataId) {
            return $structure;
        }

        foreach ($structure->getStructures() as $childStructure) {
            $result = $this->findStructureByDataId($childStructure, $dataId);
            if ($result) {
                return $result;
            }
        }

        return null;
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
        $publishSlaves = ['elements' => [], 'languages' => []];

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
                        $publishSlaves['elements'][] = [$teaser->getId(), $slaveLanguage, $elementVersion->getVersion(), 'async', 1];
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
                        $publishSlaves['elements'][] = [$node->getId(), $slaveLanguage, 0, 'async', 1];
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
    private function publishTeaser(ElementVersion $elementVersion, Teaser $teaser = null, $language, $userId, $comment = null, array $publishSlaves = [])
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
    private function publishTreeNode(ElementVersion $elementVersion, TreeNodeInterface $treeNode, $language, $userId, $comment = null, array $publishSlaves = [])
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
