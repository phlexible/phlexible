<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Controller;

use Phlexible\Bundle\AccessControlBundle\ContentObject\ContentObjectInterface;
use Phlexible\Bundle\ElementBundle\Controller\Data\DataSaver;
use Phlexible\Bundle\ElementBundle\ElementEvents;
use Phlexible\Bundle\ElementBundle\ElementStructure\Diff\Diff;
use Phlexible\Bundle\ElementBundle\ElementStructure\Serializer\ArraySerializer as ElementArraySerializer;
use Phlexible\Bundle\ElementBundle\Entity\ElementLock;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\ElementBundle\Event\LoadDataEvent;
use Phlexible\Bundle\ElementBundle\Event\SaveElementEvent;
use Phlexible\Bundle\ElementBundle\Event\SaveNodeDataEvent;
use Phlexible\Bundle\ElementBundle\Model\ElementHistoryManagerInterface;
use Phlexible\Bundle\ElementBundle\Model\ElementStructure;
use Phlexible\Bundle\ElementBundle\Model\ElementStructureValue;
use Phlexible\Bundle\ElementtypeBundle\Entity\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeStructure\Serializer\ArraySerializer as ElementtypeArraySerializer;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\SecurityBundle\Acl\Acl;
use Phlexible\Bundle\TreeBundle\Doctrine\TreeFilter;
use Phlexible\Bundle\TreeBundle\Event\NodeEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Data controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/elements/data")
 * @Security("is_granted('elements')")
 */
class DataController extends Controller
{
    /**
     * Load element data
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/load", name="elements_data_load")
     */
    public function loadAction(Request $request)
    {
        $treeId = (int) $request->get('id');
        $teaserId = (int) $request->get('teaser_id');
        $language = $request->get('language');
        $version = $request->get('version');
        $unlockId = $request->get('unlock');
        $doLock = (bool) $request->get('lock', false);

        $teaserManager = $this->get('phlexible_teaser.teaser_manager');
        $treeManager = $this->get('phlexible_tree.tree_manager');
        $elementService = $this->get('phlexible_element.element_service');
        $iconResolver = $this->get('phlexible_element.icon_resolver');
        $stateManager = $this->get('phlexible_tree.state_manager');
        $elementHistoryManager = $this->get('phlexible_element.element_history_manager');
        $lockManager = $this->get('phlexible_element.element_lock_manager');
        $userManager = $this->get('phlexible_user.user_manager');
        $securityContext = $this->get('security.context');

        try {
            $teaser = null;
            if ($teaserId) {
                $teaser = $teaserManager->findTeaser($teaserId);
                $eid = $teaser->getTypeId();
                $treeId = $teaser->getTreeId();
                $tree = $treeManager->getByNodeId($treeId);
                $node = $tree->get($treeId);
            } elseif ($treeId) {
                $tree = $treeManager->getByNodeId($treeId);
                $node = $tree->get($treeId);
                $eid = $node->getTypeId();
            } else {
                throw new \Exception('Unknown data requested.');
            }

            $element = $elementService->findElement($node->getTypeId());
            $elementMasterLanguage = $element->getMasterLanguage();

            if (!$language) {
                $language = $elementMasterLanguage;
            }

            if ($teaser) {
                $isPublished = $teaser->isPublished($language);
                $onlineVersion = null;
            } elseif ($treeId) {
                $isPublished = $stateManager->isPublished($node, $language);
                $onlineVersion = $stateManager->getPublishedVersion($node, $language);
            } else {
                throw new \Exception('Unknown data requested.');
            }

            if ($version) {
                $elementVersion = $elementService->findElementVersion($element, $version);
            } else {
                $elementVersion = $elementService->findLatestElementVersion($element);
                $version = $elementVersion->getVersion();
            }

            $elementtypeService = $elementService->getElementtypeService();

            $elementtype = $elementService->findElementtype($element);
            $elementtypeVersion = $elementService->findElementtypeVersion($elementVersion);
            $elementtypeStructure = $elementtypeService->findElementtypeStructure($elementtypeVersion);
            $type = $elementtype->getType();

            // versions

            if ($teaser) {
                $publishedVersions = $elementHistoryManager->findBy(
                    array(
                        'teaserId' => $teaser->getId(),
                        'action'   => 'publishTeaser'
                    )
                );
            } else {
                $publishedVersions = $elementHistoryManager->findBy(
                    array(
                        'treeId' => $node->getId(),
                        'action' => 'publishNode'
                    )
                );
            }

            $versions = array();
            foreach (array_reverse($elementService->getVersions($element)) as $version) {
                $versions[$version] = array(
                    'version'       => $version,
                    'format'        => 2,
                    'create_date'   => date('Y-m-d H:i:s'),
                    'is_published'  => false,
                    'was_published' => false,
                );
            }

            foreach ($publishedVersions as $publishedVersion) {
                $versions[$publishedVersion->getVersion()]['online'] = true;
                if ($publishedVersion->getVersion() === $onlineVersion) {
                    $versions[$publishedVersion->getVersion()]['is_published'] = true;
                } else {
                    $versions[$publishedVersion->getVersion()]['was_published'] = true;
                }
            }

            $versions = array_values($versions);

            // instances

            $instances = array();
            if ($teaser) {
                // TODO: implement $teaserManager->getInstances()
                foreach ($teaserManager->getInstances($teaser) as $instanceTeaser) {
                    $instance = array(
                        'id'              => $instanceTeaser->getId(),
                        'instance_master' => false,
                        'modify_time'     => $instanceTeaser->getCreatedAt()->format('Y-m-d H:i:s'),
                        'icon'            => $iconResolver->resolveTeaser($instanceTeaser, $language),
                        'type'            => 'teaser',
                        'link'            => array(),
                    );

                    $instances[] = $instance;
                }
            } else {
                foreach ($tree->getInstances($node) as $instanceNode) {
                    $instance = array(
                        'id'              => $instanceNode->getId(),
                        'instance_master' => false,
                        'modify_time'     => $instanceNode->getCreatedAt()->format('Y-m-d H:i:s'),
                        'icon'            => $iconResolver->resolveTreeNode($instanceNode, $language),
                        'type'            => 'treenode',
                        'link'            => array(),
                    );

                    if ($instanceNode->getTree()->getSiterootId() !== $tree->getSiterootId()) {
                        $instance['link'] = array(
                            'start_tid_path' => '/' . implode('/', $instanceNode->getTree()->getIdPath($instanceNode)),
                        );
                    }

                    $instances[] = $instance;
                }
            }

            // allowed child elements

            $allowedChildren = array();
            if (!$teaser) {
                foreach ($elementtypeService->findAllowedChildrenIds($elementtype) as $allowedChildId) {
                    $childElementtype = $elementtypeService->findElementtype($allowedChildId);

                    if ($childElementtype->getType() !== 'full') {
                        continue;
                    }

                    $allowedChildren[] = array(
                        $allowedChildId,
                        $childElementtype->getTitle(),
                        $iconResolver->resolveElementtype($childElementtype),
                    );
                }
            }

            // diff

            $diff = $request->get('diff');
            $diffVersionFrom = $request->get('diff_version_from');
            $diffVersionTo = $request->get('diff_version_to');
            $diffLanguage = $request->get('diff_language');

            if ($diff && $diffVersionTo) {
                $fromElementVersion = $elementService->findElementVersion($element, $diffVersionFrom);
                $fromElementStructure = $elementService->findElementStructure($fromElementVersion, $diffLanguage);
                $toElementVersion = $elementService->findElementVersion($element, $diffVersionTo);
                $toElementStructure = $elementService->findElementStructure($toElementVersion, $diffLanguage);
                $differ = new Diff();
                $elementStructure = $differ->diff($fromElementStructure, $toElementStructure);
            } else {
                $elementStructure = $elementService->findElementStructure($elementVersion, $language);
            }

            $diffInfo = null;
            if ($diff) {
                $diffInfo = array(
                    'enabled'      => $diff,
                    'version_from' => $diffVersionFrom,
                    'version_to'   => $diffVersionTo,
                    'language'     => $diffLanguage,
                );
            }

            // lock

            if ($unlockId !== null) {
                $unlockElement = $elementService->findElement($unlockId);
                if ($lockManager->isLockedByUser($unlockElement, $language, $this->getUser()->getId())) {
                    try {
                        $lockManager->unlock($unlockElement, $this->getUser()->getId());
                    } catch (\Exception $e) {
                        // unlock failed
                    }
                }
            }

            if ($node instanceof ContentObjectInterface) {
                if (!$securityContext->isGranted(Acl::RESOURCE_SUPERADMIN) &&
                    !$securityContext->isGranted(array('right' => 'EDIT', 'language' => $language), $node)
                ) {
                    $doLock = false;
                }
            }

            $lock = null;
            if ($doLock && !$diff) {
                if (!$lockManager->isLockedByOtherUser($element, $language, $this->getUser()->getId())) {
                    $lock = $lockManager->lock(
                        $element,
                        $this->getUser()->getId(),
                        $language
                    );
                }
            }

            if (!$lock) {
                $lock = $lockManager->findMasterLock($element);
                if (!$lock) {
                    $lock = $lockManager->findSlaveLock($element, $language);
                }
            }

            $lockInfo = null;

            if ($lock && !$diff) {
                $lockUser = $userManager->find($lock->getUserId());

                $lockInfo = array(
                    'status'   => 'locked',
                    'id'       => $lock->getEid(),
                    'username' => $lockUser->getDisplayName(),
                    'time'     => $lock->getLockedAt()->format('Y-m-d H:i:s'),
                    'age'      => time() - $lock->getLockedAt()->format('U'),
                    'type'     => $lock->getType(),
                );

                if ($lock->getUserId() === $this->getUser()->getId()) {
                    $lockInfo['status'] = 'edit';
                } elseif ($lock->getType() == ElementLock::TYPE_PERMANENTLY) {
                    $lockInfo['status'] = 'locked_permanently';
                }
            } elseif ($diff) {
                // Workaround for loading diffs without locking and view-mask
                // TODO: introduce new diff lock mode

                $lockInfo = array(
                    'status'   => 'edit',
                    'id'       => '',
                    'username' => '',
                    'time'     => '',
                    'age'      => 0,
                    'type'     => ElementLock::TYPE_TEMPORARY,
                );
            }

            // meta

            $meta = array();
            $elementMetaSetResolver = $this->get('phlexible_element.element_meta_set_resolver');
            $elementMetaDataManager = $this->get('phlexible_element.element_meta_data_manager');
            $optionResolver = $this->get('phlexible_meta_set.option_resolver');
            $metaSetId = $elementtypeVersion->getMetaSetId();

            if ($metaSetId) {
                $metaSet = $elementMetaSetResolver->resolve($elementVersion);
                $metaData = $elementMetaDataManager->findByMetaSetAndElementVersion($metaSet, $elementVersion);

                $fieldDatas = array();

                foreach ($metaSet->getFields() as $field) {
                    $options = $optionResolver->resolve($field);

                    $fieldData = array(
                        'key'          => $field->getName(),
                        'type'         => $field->getType(),
                        'options'      => $options,
                        'readonly'     => $field->isReadonly(),
                        'required'     => $field->isRequired(),
                        'synchronized' => $field->isSynchronized(),
                    );

                    if ($metaData) {
                        foreach ($metaData->getLanguages() as $metaLanguage) {
                            if ($language === $metaLanguage) {
                                $fieldData['value'] = $metaData->get($field->getId(), $language);
                                break;
                            }
                        }
                    }

                    $fieldDatas[] = $fieldData;
                }

                $meta = array(
                    'set_id' => $metaSetId,
                    'title'  => $metaSet->getName(),
                    'fields' => $fieldDatas
                );
            }

            // redirects
            // TODO: auslagern

            $redirects = array();
            if (!$teaser && $this->container->has('redirectsManager')) {
                $redirectsManager = $this->get('redirectsManager');
                $redirects = $redirectsManager->getForTidAndLanguage($treeId, $language);
            }

            // preview / online url

            $urls = array(
                'preview' => '',
                'online'  => '',
            );

            $publishDate = null;
            $publishUser = null;
            $onlineVersion = null;
            $latestVersion = null;

            if (in_array($elementtype->getType(), array(Elementtype::TYPE_FULL, Elementtype::TYPE_STRUCTURE, Elementtype::TYPE_PART))) {
                if ($type == Elementtype::TYPE_FULL) {
                    $urls['preview'] = $this->generateUrl('frontend_preview', array('id' => $node->getId(), 'language' => $language));

                    if ($isPublished) {
                        $contentNode = $this->get('phlexible_tree.content_tree_manager.delegating')->findByTreeId($node->getId())->get($node->getId());
                        $urls['online'] = $this->generateUrl($contentNode);
                    }
                }

                if ($isPublished) {
                    $publishInfo = $stateManager->getPublishInfo($node, $language);
                    $publishDate = $publishInfo['published_at'];
                    $publishUserId = $publishInfo['publish_user_id'];
                    $publishUser = $userManager->find($publishUserId);
                    $onlineVersion = $publishInfo['version'];
                }

                $latestVersion = $element->getLatestVersion();
            }

            // attributes
            // TODO: implement $teaser->getAttributes()

            if ($teaser) {
                $attributes = $teaser->getAttributes();
            } else {
                $attributes = $node->getAttributes();
            }

            // context
            // TODO: repair element context

            $context = array();
            if (0) {
                $contextManager = $this->get('phlexible_element.context.manager');

                if ($contextManager->useContext()) {
                    $contextCountries = $contextManager->getAllCountries();

                    $activeContextCountries = $teaserId
                        ? $contextManager->getActiveCountriesByTeaserId($teaserId)
                        : $contextManager->getActiveCountriesByTid($node->getId());

                    foreach ($contextCountries as $contextKey => $contextValue) {
                        $context[] = array(
                            'id'      => $contextKey,
                            'country' => $contextValue,
                            'active'  => in_array($contextKey, $activeContextCountries) ? 1 : 0
                        );
                    }
                }
            }

            // pager

            $pager = array();
            if (!$teaser) {
                $parentNode = $tree->getParent($node);
                if ($parentNode) {
                    $parentElement = $elementService->findElement($parentNode->getTypeId());
                    $parentElementtype = $elementService->findElementtype($parentElement);
                    if ($parentElementtype->getHideChildren()) {
                        $filter = new TreeFilter(
                            $this->get('doctrine.dbal.default_connection'),
                            $request->getSession(),
                            $this->get('event_dispatcher'),
                            $parentNode->getId(),
                            $language
                        );
                        $pager = $filter->getPager($node->getId());
                    }
                }
            }

            // rights

            $userRights = array();
            if ($node instanceof ContentObjectInterface) {
                if (!$securityContext->isGranted(Acl::RESOURCE_SUPERADMIN)) {
                    //$contentRightsManager->calculateRights('internal', $rightsNode, $rightsIdentifiers);

                    if ($securityContext->isGranted(array('right' => 'VIEW', 'language' => $language), $node)) {
                        return null;
                    }

                    $userRights = array(); //$contentRightsManager->getRights($language);
                    $userRights = array_keys($userRights);
                } else {
                    $userRights = array_keys(
                        $this->get('phlexible_access_control.permissions')->getByContentClass('Phlexible\Bundle\TreeBundle\Model\TreeNode')
                    );
                }
            }

            $status = '';
            if ($stateManager->isPublished($node, $language)) {
                $status = $stateManager->isAsync($node, $language) ? 'async' : 'online';
            }

            $icon = $iconResolver->resolveTreeNode($node, $language);

            $createUser = $userManager->find($elementVersion->getCreateUserId());

            // glue together

            $properties = array(
                'tid'              => $treeId,
                'eid'              => $eid,
                'siteroot_id'      => empty($teaserId) ? $node->getTree()->getSiterootId() : null,
                'teaser_id'        => $teaserId,
                'language'         => $language,
                'version'          => $elementVersion->getVersion(),
                'is_published'     => $isPublished,
                'master'           => $language == $element->getMasterLanguage() ? true : false,
                'status'           => $status,
                'backend_title'    => substr(
                    strip_tags($elementVersion->getBackendTitle($language, $elementMasterLanguage)),
                    0,
                    30
                ),
                'page_title'       => substr(
                    strip_tags($elementVersion->getPageTitle($language, $elementMasterLanguage)),
                    0,
                    30
                ),
                'navigation_title' => substr(
                    strip_tags($elementVersion->getNavigationTitle($language, $elementMasterLanguage)),
                    0,
                    30
                ),
                'unique_id'        => $element->getUniqueID(),
                'et_id'            => $elementtype->getId(),
                'et_title'         => $elementtype->getTitle(),
                'et_version'       => $elementVersion->getElementTypeVersion()
                    . ' [' . $elementtypeService->findLatestElementtypeVersion($elementtype)->getVersion() . ']',
                'et_unique_id'     => $elementtype->getUniqueId(),
                'et_type'          => $elementtype->getType(),
                'author'           => $createUser->getDisplayName(),
                'create_date'      => $elementVersion->getCreatedAt()->format('Y-m-d H:i:s'),
                'publish_date'     => $publishDate,
                'publisher'        => $publishUser ? $publishUser->getDisplayName() : null,
                'latest_version'   => (int) $latestVersion,
                'online_version'   => (int) $onlineVersion,
                'masterlanguage'   => $elementMasterLanguage,
                'sort_mode'        => $node->getSortMode(),
                'sort_dir'         => $node->getSortDir(),
                'icon'             => $icon,
                'navigation'       => $node->getInNavigation(),
            );

            $elementtypeSerializer = new ElementtypeArraySerializer();
            $serializedStructure = $elementtypeSerializer->serialize($elementtypeStructure);

            $elementSerializer = new ElementArraySerializer();
            $serializedValues = $elementSerializer->serialize($elementStructure);

            $data = array(
                'success'             => true,
                'properties'          => $properties,
                'attributes'          => $attributes,
                'comment'             => $elementVersion->getComment(),
                'meta'                => $meta,
                'redirects'           => $redirects,
                'default_tab'         => $elementtype->getDefaultTab(),
                'default_content_tab' => $elementtypeVersion->getDefaultContentTab(),
                'lockinfo'            => $lockInfo,
                'diff'                => $diffInfo,
                'urls'                => $urls,
                'context'             => $context,
                'pager'               => $pager,
                'rights'              => $userRights,
                'instances'           => $instances,
                'children'            => $allowedChildren,
                'versions'            => $versions,
                'valueStructure'      => $serializedValues,
                'structure'           => $serializedStructure,
            );

            $data = (object) $data;
            $event = new LoadDataEvent($node, $teaser, $language, $data);
            $this->get('event_dispatcher')->dispatch(ElementEvents::LOAD_DATA, $event);
            $data = (array) $data;
        } catch (\Exception $e) {
            $data = array(
                'success' => false,
                'msg'     => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            );
            if ($e->getPrevious()) {
                $data['previous'] = array(
                    'msg'   => $e->getPrevious()->getMessage(),
                    'trace' => $e->getPrevious()->getTraceAsString(),
                );
            }
        }

        return new JsonResponse($data);
    }

    /**
     * Save element data
     *
     * @param Request $request
     *
     * @throws \Exception
     * @return ResultResponse
     * @Route("/save", name="elements_data_save")
     */
    public function saveAction(Request $request)
    {
        $tid = $request->get('tid');
        $teaserId = $request->get('teaser_id');
        $eid = $request->get('eid');
        $language = $request->get('language');
        $data = $request->get('data');
        $oldVersion = $request->get('version');
        $comment = $request->get('comment');
        $isPublish = $request->get('publish');
        $notifications = $request->get('notifications');
        $values = $request->request->all();

        $saver = new DataSaver(
            $this->get('phlexible_element.element_service'),
            $this->get('phlexible_tree.tree_manager'),
            $this->get('phlexible_teaser.teaser_manager'),
            $this->get('event_dispatcher')
        );
        $elementStructure = $saver->save($request, $this->getUser());
die;

        if ($data) {
            $data = json_decode($data, true);
        }

        $dispatcher = $this->get('event_dispatcher');
        $treeManager = $this->get('phlexible_tree.tree_manager');
        $teaserManager = $this->get('phlexible_teaser.teaser_manager');
        $elementService = $this->get('phlexible_element.element_service');
        $stateManager = $this->get('phlexible_tree.state_manager');
        $elementHistoryManager = $this->get('phlexible_element.element_history_manager');

        $tree = $treeManager->getByNodeId($tid);
        $node = $tree->get($tid);
        $element = $elementService->findElement($eid);
        $elementtype = $elementService->findElementtype($element);
        $oldElementVersion = $elementService->findLatestElementVersion($element);
        $elementtypeVersion = $elementService->findElementtypeVersion($oldElementVersion);
        $oldLatestVersion = $oldElementVersion->getVersion();
        $isMaster = $element->getMasterLanguage() == $language;

        $publishSlaveLanguages = array();
        $publishSlaves = array();

        if ($isPublish) {
            if ($element->getMasterLanguage() == $language) {
                foreach (explode(',', $this->container->getParameter('phlexible_cms.languages.available')) as $slaveLanguage) {
                    if ($language == $slaveLanguage) {
                        continue;
                    }

                    if ($teaser) {
                        if ($teaser->isPublished($slaveLanguage)) {
                            if (!$teaser->isAsync($slaveLanguage)) {
                                $publishSlaveLanguages[] = $slaveLanguage;
                            }
                        } else {
                            if ($this->container->getParameter(
                                'phlexible_element.publish.cross_language_publish_offline'
                            )
                            ) {
                                $publishSlaves[] = array($teaser->getId(), $slaveLanguage, 0, '', 0);
                            }
                        }
                    } else {
                        if ($stateManager->isPublished($node, $slaveLanguage)) {
                            if (!$stateManager->isAsync($node, $slaveLanguage)) {
                                $publishSlaveLanguages[] = $slaveLanguage;
                            } else {
                                $publishSlaves[] = array($node->getId(), $slaveLanguage, 0, 'async', 1);
                            }
                        } else {
                            if ($this->container->getParameter('phlexible_element.publish.cross_language_publish_offline')) {
                                $publishSlaves[] = array($node->getId(), $slaveLanguage, 0, '', 0);
                            }
                        }
                    }
                }
            }
        }

        foreach ($publishSlaves as $publishSlaveKey => $publishSlaveRow) {
            $publishSlaves[$publishSlaveKey][2] = $newVersion;
        }

        if ($teaser) {
            $elementHistoryManager->insert(
                ElementHistoryManagerInterface::ACTION_CREATE_ELEMENT_VERSION,
                $element->getEid(),
                $elementVersion->getCreateUserId(),
                null,
                $teaser->getId(),
                $elementVersion->getVersion(),
                $language,
                $comment
            );
        } else {
            $elementHistoryManager->insert(
                ElementHistoryManagerInterface::ACTION_CREATE_ELEMENT_VERSION,
                $element->getEid(),
                $elementVersion->getCreateUserId(),
                $node->getId(),
                null,
                $elementVersion->getVersion(),
                $language,
                $comment
            );
        }

        // Copy meta values from old version to new version
        // TODO: repair

        $setId = $elementtypeVersion->getMetaSetId();
        if (0 && $setId) {
            $select = $db
                ->select()
                ->from($db->prefix . 'element_version_metaset_items')
                ->where('set_id = ?', $setId)
                ->where('eid = ?', $eid)
                ->where('version = ?', $oldLatestVersion);

            foreach ($db->fetchAll($select) as $insertData) {
                unset($insertData['id']);
                $insertData['version'] = $newVersion;

                $db->insert($db->prefix . 'element_version_metaset_items', $insertData);
            }
        }

        // save element structure
        if ($isMaster) {
            //$elementData->saveData($elementVersion, $values, $oldLatestVersion);
        } else {
            //$elementData->saveData($elementVersion, $values, $oldLatestVersion, $element->getMasterLanguage());
        }

        // update sort
        // TODO: repair

        if (0 && !$teaser) {
            $elementVersion->getBackendTitle($language);

            $select = $db
                ->select()
                ->distinct()
                ->from($db->prefix . 'element_tree', 'parent_id')
                ->where('eid = ?', $eid);

            $updateTids = $db->fetchCol($select);

            $parentNode = $node->getParentNode();
            if ($parentNode && $parentNode->getSortMode() != Tree::SORT_MODE_FREE) {
                foreach ($updateTids as $updateTid) {
                    if (!$updateTid) {
                        continue;
                    }

                    $sorter = $this->get('elementsTreeSorter');
                    $sorter->sortNode($parentNode);
                }
            }
        }

        $msg = 'Element "' . $eid . '" master language "' . $language . '" saved as new version ' . $newVersion;

        $publishOther = array();
        if ($isPublish) {
            $msg .= ' and published.';

            if (!$teaser) {
                $node = $treeManager->getNodeByNodeId($tid);
                $tree = $node->getTree();

                // notification data
                $notificationManager = $this->get('elementsNotifications');
                $checkNotify = $notificationManager->getNotificationByTid($tid, $language);

                // check if there is a notification already
                if (count($checkNotify) && $notifications) {
                    $notificationId = $checkNotify[0]['id'];
                    $notificationManager->update($notificationId, $language);
                } else {
                    if ($notifications) {
                        $notificationManager->save($tid, $language);
                    }
                }

                // publish node
                $tree->publishNode(
                    $node,
                    $language,
                    $newVersion,
                    false,
                    $comment
                );

                if (count($publishSlaveLanguages)) {
                    foreach ($publishSlaveLanguages as $slaveLanguage) {
                        // publish slave node
                        $tree->publishNode(
                            $node,
                            $slaveLanguage,
                            $newVersion,
                            false,
                            $comment
                        );

                        // workaround to fix missing catch results for non master language elements
                        Makeweb_Elements_Element_History::insert(
                            Makeweb_Elements_Element_History::ACTION_SAVE,
                            $eid,
                            $newVersion,
                            $slaveLanguage
                        );
                    }
                }
            } else {
                $tree = $node->getTree();

                $eid = $teasersManager->publish(
                    $teaserId,
                    $newVersion,
                    $language,
                    $comment,
                    $tid
                );

                if (count($publishSlaveLanguages)) {
                    foreach ($publishSlaveLanguages as $slaveLanguage) {
                        // publish slave node
                        $teasersManager->publish(
                            $teaserId,
                            $newVersion,
                            $slaveLanguage,
                            $comment,
                            $tid
                        );
                    }
                }
            }
        } else {
            $msg .= '.';
        }

        // remove locks

        $lockManager = $this->get('phlexible_element.element_lock_manager');
        $lockManager->unlock($element, $language);

        // queue update job
        // TODO: repair

        $queueService = $this->get('phlexible_queue.job_manager');

        /*
        $updateUsageJob = new Makeweb_Elements_Job_UpdateUsage();
        $updateUsageJob->setEid($eid);
        $queueService->addUniqueJob($updateUsageJob);

        $updateCatchHelperJob = new Makeweb_Teasers_Job_UpdateCatchHelper();
        $updateUsageJob->setEid($eid);
        $queueManager->addJob($updateCatchHelperJob);
        */

        // update file usage

        /*
        $fileUsage = new Makeweb_Elements_Element_FileUsage(MWF_Registry::getContainer()->dbPool);
        $fileUsage->update($eid);
        */

        $data = array();

        $status = '';
        if ($stateManager->isPublished($node, $language)) {
            $status = $stateManager->isAsync($node, $language) ? 'async' : 'online';
        }

        $data = array(
            'title'         => $elementVersion->getBackendTitle($language),
            'status'        => $status,
            'navigation'    => $teaserId ? '' : $node->getInNavigation($newVersion),
            'restricted'    => $teaserId ? '' : $node->getAttribute('restrictire'),
            'publish_other' => $publishSlaves,
        );

        return new ResultResponse(true, $msg, $data);
    }
}
