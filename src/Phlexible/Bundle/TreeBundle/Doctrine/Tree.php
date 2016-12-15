<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\ElementBundle\Model\ElementHistoryManagerInterface;
use Phlexible\Bundle\TreeBundle\Entity\TreeNode;
use Phlexible\Bundle\TreeBundle\Event\DeleteNodeEvent;
use Phlexible\Bundle\TreeBundle\Event\MoveNodeEvent;
use Phlexible\Bundle\TreeBundle\Event\NodeEvent;
use Phlexible\Bundle\TreeBundle\Event\PublishNodeEvent;
use Phlexible\Bundle\TreeBundle\Event\ReorderChildNodesEvent;
use Phlexible\Bundle\TreeBundle\Event\ReorderNodeEvent;
use Phlexible\Bundle\TreeBundle\Event\SetNodeOfflineEvent;
use Phlexible\Bundle\TreeBundle\Exception\InvalidNodeMoveException;
use Phlexible\Bundle\TreeBundle\Model\StateManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Phlexible\Bundle\TreeBundle\Model\WritableTreeInterface;
use Phlexible\Bundle\TreeBundle\Tree\TreeIterator;
use Phlexible\Bundle\TreeBundle\TreeEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

/**
 * Database tree
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Tree implements TreeInterface, WritableTreeInterface
{
    /**
     * @var string
     */
    private $siterootId;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ElementHistoryManagerInterface
     */
    private $historyManager;

    /**
     * @var StateManagerInterface
     */
    private $stateManager;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param string                         $siterootId
     * @param EntityManager                  $entityManager
     * @param ElementHistoryManagerInterface $historyManager
     * @param StateManagerInterface          $stateManager
     * @param EventDispatcherInterface       $dispatcher
     *
     * @throws \InvalidArgumentException if empty siteroot id given
     */
    public function __construct(
        $siterootId,
        EntityManager $entityManager,
        ElementHistoryManagerInterface $historyManager,
        StateManagerInterface $stateManager,
        EventDispatcherInterface $dispatcher
    ) {
        Assert::notEmpty($siterootId, 'Empty siteroot id given');
        $this->siterootId = $siterootId;
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
        $this->historyManager = $historyManager;
        $this->stateManager = $stateManager;
    }

    /**
     * {@inheritdoc}
     *
     * @return TreeIterator
     */
    public function getIterator()
    {
        return new TreeIterator($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getSiterootId()
    {
        return $this->siterootId;
    }

    /**
     * @return EntityRepository
     */
    private function getTreeNodeRepository()
    {
        return $this->entityManager->getRepository('PhlexibleTreeBundle:TreeNode');
    }

    /**
     * {@inheritdoc}
     */
    public function getRoot()
    {
        $node = $this->getTreeNodeRepository()->findOneBy(['siterootId' => $this->siterootId, 'parentNode' => null]);
        $node->setTree($this);

        return $node;
    }

    /**
     * @var TreeNodeInterface
     */
    private $nodes = [];

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        if (!isset($this->nodes[$id]) || $this->nodes[$id] === null) {
            $node = $this->getTreeNodeRepository()->findOneBy(['siterootId' => $this->siterootId, 'id' => $id]);
            if ($node) {
                $node->setTree($this);
            }
            $this->nodes[$id] = $node;
        }

        return $this->nodes[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        return $this->get($id) ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getByTypeId($typeId, $type = null)
    {
        $criteria = ['typeId' => $typeId, 'siterootId' => $this->siterootId];
        if ($type) {
            $criteria['type'] = $type;
        }
        $nodes = $this->getTreeNodeRepository()->findBy($criteria);
        foreach ($nodes as $node) {
            $node->setTree($this);
        }

        return $nodes;
    }

    /**
     * {@inheritdoc}
     */
    public function hasByTypeId($typeId, $type = null)
    {
        return $this->getByTypeId($typeId, $type) ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren(TreeNodeInterface $node)
    {
        $nodes = $this->getTreeNodeRepository()->findBy(['siterootId' => $this->siterootId, 'parentNode' => $node->getId()], ['sort' => 'ASC']);
        foreach ($nodes as $node) {
            $node->setTree($this);
            $this->nodes[$node->getId()] = $node;
        }

        return $nodes;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildren(TreeNodeInterface  $node)
    {
        return count($this->getChildren($node)) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(TreeNodeInterface $node)
    {
        $parentNode = $node->getParentNode();
        if ($parentNode) {
            $parentNode->setTree($this);
        }

        return $parentNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdPath(TreeNodeInterface $node)
    {
        $ids = [];
        foreach ($this->getPath($node) as $pathNode) {
            $ids[] = $pathNode->getId();
        }

        return $ids;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(TreeNodeInterface $node)
    {
        $path = [];

        do {
            $path[$node->getId()] = $node;
        } while ($node = $this->getParent($node));

        $path = array_reverse($path, true);

        return $path;
    }

    /**
     * {@inheritdoc}
     */
    public function isRoot(TreeNodeInterface $node)
    {
        return $this->getRoot() === $node;
    }

    /**
     * {@inheritdoc}
     */
    public function isChildOf(TreeNodeInterface $childNode, TreeNodeInterface $parentNode)
    {
        $path = $this->getIdPath($childNode);

        foreach ($path as $id) {
            if ($parentNode->getId() === $id) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isParentOf(TreeNodeInterface $parentNode, TreeNodeInterface $childNode)
    {
        return $this->isChildOf($childNode, $parentNode);
    }

    /**
     * {@inheritdoc}
     */
    public function isInstance(TreeNodeInterface $node)
    {
        return count($this->getInstances($node)) > 1;
    }

    /**
     * {@inheritdoc}
     */
    public function isInstanceMaster(TreeNodeInterface $node)
    {
        return $node->getAttribute('instanceMaster', false);
    }

    /**
     * {@inheritdoc}
     */
    public function getInstances(TreeNodeInterface $node)
    {
        $nodes = $this->getTreeNodeRepository()->findBy(['siterootId' => $this->siterootId, 'type' => $node->getType(), 'typeId' => $node->getTypeId()]);
        foreach ($nodes as $node) {
            $node->setTree($this);
        }

        return $nodes;
    }

    /**
     * {@inheritdoc}
     */
    public function getSavedLanguages(TreeNodeInterface $treeNode)
    {
        $actions = $this->historyManager->findBy(
            array(
                'eid' => $treeNode->getTypeId(),
                'action' => ElementHistoryManagerInterface::ACTION_CREATE_ELEMENT_VERSION
            )
        );

        $languages = array();
        foreach ($actions as $action) {
            $languages[$action->getLanguage()] = $action->getLanguage();
        }

        $languages = array_filter(array_values($languages), function ($value) {
            return !is_null($value);
        });

        return $languages;
    }

    /**
     * {@inheritdoc}
     */
    public function isPublished(TreeNodeInterface $node, $language)
    {
        return $this->stateManager->isPublished($node, $language);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedLanguages(TreeNodeInterface $node)
    {
        return $this->stateManager->getPublishedLanguages($node);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedVersion(TreeNodeInterface $node, $language)
    {
        return $this->stateManager->getPublishedVersion($node, $language);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedVersions(TreeNodeInterface $node)
    {
        return $this->stateManager->getPublishedVersions($node);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedAt(TreeNodeInterface $node, $language)
    {
        return $this->stateManager->getPublishedAt($node, $language);
    }

    /**
     * {@inheritdoc}
     */
    public function isAsync(TreeNodeInterface $node, $language)
    {
        return $this->stateManager->isAsync($node, $language);
    }

    /**
     * {@inheritdoc}
     */
    public function findOnlineByTreeNode(TreeNodeInterface $node)
    {
        return $this->stateManager->findByTreeNode($node);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneOnlineByTreeNodeAndLanguage(TreeNodeInterface $node, $language)
    {
        return $this->stateManager->findOneByTreeNodeAndLanguage($node, $language);
    }

    /**
     * @param TreeNodeInterface $node
     * @param bool              $flush
     *
     * @return $this
     */
    public function updateNode(TreeNodeInterface $node, $flush = true)
    {
        $node->setSiterootId($this->siterootId);

        $this->entityManager->persist($node);
        if ($flush) {
            $this->entityManager->flush($node);
        }

        return $this;
    }

    /**
     * @param TreeNodeInterface[] $nodes
     * @param bool                $flush
     *
     * @return $this
     */
    public function updateNodes(array $nodes, $flush = true)
    {
        foreach ($nodes as $node) {
            $node->setSiterootId($this->siterootId);

            $this->entityManager->persist($node);
        }

        if ($flush) {
            $this->entityManager->flush();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function init($type, $typeId, $userId)
    {
        $node = new TreeNode();
        $node
            ->setTree($this)
            ->setParentNode(null)
            ->setType($type)
            ->setTypeId($typeId)
            ->setCreateUserId($userId)
            ->setCreatedAt(new \DateTime);

        $event = new NodeEvent($node);
        if ($this->dispatcher->dispatch(TreeEvents::BEFORE_CREATE_NODE, $event)->isPropagationStopped()) {
            return false;
        }

        $this->updateNode($node);

        // history
        $this->historyManager->insert(ElementHistoryManagerInterface::ACTION_CREATE_NODE, $typeId, $userId, $node->getId());

        $event = new NodeEvent($node);
        $this->dispatcher->dispatch(TreeEvents::CREATE_NODE, $event);

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    public function create(
        TreeNodeInterface $parentNode,
        TreeNodeInterface $afterNode = null,
        $type,
        $typeId,
        array $attributes,
        $userId,
        $sortMode = 'free',
        $sortDir = 'asc',
        $navigation = false,
        $needAuthentication = false)
    {
        if (!$parentNode instanceof TreeNodeInterface) {
            $parentNode = $this->get($parentNode);
        }
        if ($afterNode && !$afterNode instanceof TreeNodeInterface) {
            $afterNode = $this->get($afterNode);
        }

        $sort = 0;
        $sortNodes = [];
        if ($parentNode->getSortMode() === 'free') {
            if ($afterNode) {
                $sort = $afterNode->getSort() + 1;
            }

            foreach ($this->getChildren($parentNode) as $sortNode) {
                if ($sortNode->getSort() >= $sort) {
                    $sortNode->setSort($sortNode->getSort() + 1);
                    $sortNodes[] = $sortNode;
                }
            }
        }

        $node = new TreeNode();
        $node
            ->setTree($parentNode->getTree())
            ->setParentNode($parentNode)
            ->setType($type)
            ->setTypeId($typeId)
            ->setAttributes($attributes)
            ->setSort($sort)
            ->setSortMode($sortMode)
            ->setSortDir($sortDir)
            ->setInNavigation($navigation)
            ->setNeedAuthentication($needAuthentication)
            ->setCreateUserId($userId)
            ->setCreatedAt(new \DateTime);

        $event = new NodeEvent($node);
        if ($this->dispatcher->dispatch(TreeEvents::BEFORE_CREATE_NODE, $event)->isPropagationStopped()) {
            return false;
        }

        array_unshift($sortNodes, $node);
        $this->updateNodes($sortNodes);

        // history
        $this->historyManager->insert(ElementHistoryManagerInterface::ACTION_CREATE_NODE, $typeId, $userId, $node->getId());

        $event = new NodeEvent($node);
        $this->dispatcher->dispatch(TreeEvents::CREATE_NODE, $event);

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    public function createInstance(
        TreeNodeInterface $parentNode,
        TreeNodeInterface $afterNode = null,
        TreeNodeInterface $sourceNode,
        $userId
    )
    {

        $sort = 0;
        $sortNodes = [];
        if ($parentNode->getSortMode() === 'free') {
            if ($afterNode) {
                $sort = $afterNode->getSort() + 1;
            }

            foreach ($this->getChildren($parentNode) as $sortNode) {
                if ($sortNode->getSort() >= $sort) {
                    $sortNode->setSort($sortNode->getSort() + 1);
                    $sortNodes[] = $sortNode;
                }
            }
        }

        $node = new TreeNode();
        $node
            ->setTree($parentNode->getTree())
            ->setParentNode($parentNode)
            ->setType($sourceNode->getType())
            ->setTypeId($sourceNode->getTypeId())
            ->setAttributes($sourceNode->getAttributes())
            ->setSort($sort)
            ->setSortMode($sourceNode->getSortMode())
            ->setSortDir($sourceNode->getSortDir())
            ->setCreateUserId($userId)
            ->setCreatedAt(new \DateTime);

        $event = new NodeEvent($node);
        if ($this->dispatcher->dispatch(TreeEvents::BEFORE_CREATE_NODE_INSTANCE, $event)->isPropagationStopped()) {
            return false;
        }

        array_unshift($sortNodes, $node);
        $this->updateNodes($sortNodes);

        // history
        $this->historyManager->insert(ElementHistoryManagerInterface::ACTION_CREATE_NODE_INSTANCE, $node->getTypeId(), $userId, $node->getId());

        $event = new NodeEvent($node);
        $this->dispatcher->dispatch(TreeEvents::CREATE_NODE_INSTANCE, $event);

        return $node;
    }

    /**
     * Reorders node after beforeNode
     *
     * {@inheritdoc}
     */
    public function reorder(TreeNodeInterface $node, TreeNodeInterface $beforeNode)
    {
        if ($beforeNode->getParentNode()->getId() !== $node->getParentNode()->getId()) {
            throw new InvalidNodeMoveException('Node and targetNode need to have the same parent.');
        }

        if ($node->getParentNode()->getSortMode() !== 'free') {
            return;
        }

        $sort = $beforeNode->getSort() + 1;

        $event = new ReorderNodeEvent($node, $sort);
        if ($this->dispatcher->dispatch(TreeEvents::BEFORE_REORDER_NODE, $event)->isPropagationStopped()) {
            return;
        }

        $updatesNodes = [];

        $currentSort = $sort + 1;
        foreach ($this->getChildren($node->getParentNode()) as $childNode) {
            if ($childNode->getSort() <= $sort) {
                $childNode->setSort($currentSort++);
                $updatesNodes[] = $childNode;
            }
        }

        $node->setSort($sort);
        $updateNodes[] = $node;

        $this->updateNodes($updateNodes);

        $event = new NodeEvent($node);
        $this->dispatcher->dispatch(TreeEvents::REORDER_NODE, $event);

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function reorderChildren(TreeNodeInterface $node, array $sortIds)
    {
        if (count($this->getChildren($node)) !== count($sortIds)) {
            throw new InvalidNodeMoveException('Children count mismatch.');
        }

        $childNodes = array();
        foreach ($sortIds as $sort => $nodeId) {
            $childNode = $this->get($nodeId);
            if ($childNode->getParentNode()->getId() !== $node->getId()) {
                throw new InvalidNodeMoveException('Node and targetNode need to have the same parent.');
            }
            $childNodes[$sort] = $childNode;
        }

        $event = new ReorderChildNodesEvent($node, $sortIds);
        if ($this->dispatcher->dispatch(TreeEvents::BEFORE_REORDER_CHILD_NODE, $event)->isPropagationStopped()) {
            return;
        }

        foreach ($childNodes as $index => $childNode) {
            $childNode->setSort($index);
        }

        $this->updateNodes($childNodes);

        $event = new ReorderChildNodesEvent($node, $sortIds);
        $this->dispatcher->dispatch(TreeEvents::REORDER_CHILD_NODES, $event);

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function move(TreeNodeInterface $node, TreeNodeInterface $toNode, $userId)
    {
        if ($this->isChildOf($toNode, $node)) {
            throw new InvalidNodeMoveException('Invalid move.');
        }

        $oldParentId = $node->getParentNode()->getId();

        $event = new MoveNodeEvent($node, $toNode);
        if ($this->dispatcher->dispatch(TreeEvents::BEFORE_MOVE_NODE, $event)->isPropagationStopped()) {
            return;
        }

        $node
            ->setSort(0)
            ->setParentNode($toNode);

        $this->updateNode($node);

        // TODO: sort
        //$this->sorter->sortNode($toNode);

        $event = new MoveNodeEvent($node, $toNode);
        $this->dispatcher->dispatch(TreeEvents::MOVE_NODE, $event);

        // history
        $this->historyManager->insert(ElementHistoryManagerInterface::ACTION_MOVE_NODE, $node->getTypeId(), $userId, $node->getId(), null, null, null, 'Node moved from TID ' . $oldParentId . ' to TID ' . $toNode->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function delete(TreeNodeInterface $node, $userId, $comment = null)
    {
        // TODO: listener
        /*
        $rightsIdentifiers = array(
            array('uid' => $uid)
        );
        foreach (MWF_Env::getUser()->getGroups() as $group)
        {
            $rightsIdentifiers[] = array('gid' => $group->getId());
        }
        $this->_deleteCheck($node, $rightsIdentifiers);
        */

        $this->doDelete($node, $userId, $comment);
    }

    /**
     * {@inheritdoc}
     */
    public function publish(TreeNodeInterface $node, $version, $language, $userId, $comment = null)
    {
        $event = new PublishNodeEvent($node, $language, $version, false);
        if ($this->dispatcher->dispatch(TreeEvents::BEFORE_PUBLISH_NODE, $event)->isPropagationStopped()) {
            return null;
        }

        $treeNodeOnline = $this->stateManager->publish($node, $version, $language, $userId, $comment);

        $event = new PublishNodeEvent($node, $language, $version, false);
        $this->dispatcher->dispatch(TreeEvents::PUBLISH_NODE, $event);

        // history
        $this->historyManager->insert(ElementHistoryManagerInterface::ACTION_PUBLISH_NODE, $node->getTypeId(), $userId, $node->getId(), null, $version, $language);

        return $treeNodeOnline;
    }

    /**
     * {@inheritdoc}
     */
    public function setOffline(TreeNodeInterface $node, $language, $userId, $comment = null)
    {
        $event = new SetNodeOfflineEvent($node, $language);
        if ($this->dispatcher->dispatch(TreeEvents::BEFORE_SET_NODE_OFFLINE, $event)->isPropagationStopped()) {
            return null;
        }

        $this->stateManager->setOffline($node, $language, $userId, $comment);

        $event = new SetNodeOfflineEvent($node, $language);
        $this->dispatcher->dispatch(TreeEvents::SET_NODE_OFFLINE, $event);

        // history
        $this->historyManager->insert(ElementHistoryManagerInterface::ACTION_SET_NODE_OFFLINE, $node->getTypeId(), $userId, $node->getId(), null, null, $language);
    }

    /*
    protected function deleteCheck(Makeweb_Elements_Tree_Node $node, array $rightsIdentifiers)
    {
        $eid = $node->getEid();
        $uid = MWF_Env::getUid();

        $container = MWF_Registry::getContainer();

        $contentRightsManager = $container->contentRightsManager;

        if (!MWF_Env::getUser()->isGranted(MWF_Core_Acl_Acl::RESOURCE_SUPERADMIN) &&
            !MWF_Env::getUser()->isGranted(MWF_Core_Acl_Acl::RESOURCE_DEVELOPMENT)
        ) {
            $contentRightsManager->calculateRights('internal', $node, $rightsIdentifiers);

            if (true !== $contentRightsManager->hasRight('DELETE', '_all_')) {
                $msg = 'You don\t have the delete right for TID "' . $node->getId() . '"';
                throw new Makeweb_Elements_Tree_Exception($msg);
            }
        }

        $lockIdentifier = new Makeweb_Elements_Element_Identifier($eid);
        $locksService = $container->get('phlexible_element.lock.service');
        $locksRepository = $container->get('phlexible_lock.repository');

        if ($locksService->isLockedPartByOtherUser($lockIdentifier, false, $uid)) {
            $lockInfo = current($locksRepository->findByIdentifierPartAndOtherUid($lockIdentifier, $uid));
            $user = MWF_Core_Users_User_Peer::getByUserID($lockInfo->lockUid);
            $msg = 'Can\'t delete, element is locked by "' . $user->getUsername() . '".';
            throw new Makeweb_Elements_Tree_Exception_LockException($msg);
        }

        foreach ($node->getChildren() as $childNode) {
            $this->deleteCheck($childNode, $rightsIdentifiers);
        }
    }
    */

    /**
     * @param TreeNodeInterface $node
     * @param string            $userId
     * @param string            $comment
     */
    private function doDelete(TreeNodeInterface $node, $userId, $comment = null)
    {
        foreach ($this->getChildren($node) as $childNode) {
            $this->doDelete($childNode, $userId, $comment);
        }

        $nodeId = $node->getId();

        $event = new DeleteNodeEvent($node, $nodeId, $userId);
        if ($this->dispatcher->dispatch(TreeEvents::BEFORE_DELETE_NODE, $event)->isPropagationStopped()) {
            return;
        }

        $this->entityManager->remove($node);
        $this->entityManager->flush();

        $event = new DeleteNodeEvent($node, $nodeId, $userId);
        $this->dispatcher->dispatch(TreeEvents::DELETE_NODE, $event);

        // history
        $this->historyManager->insert(ElementHistoryManagerInterface::ACTION_DELETE_NODE, $node->getTypeId(), $userId, $node->getId(), null, null, null, $comment);

        // TODO: -> elements, listener
        /*
        $queueManager = $container->queueManager;
        $job = new Makeweb_Elements_Job_UpdateUsage();
        $job->setEid($eid);
        $queueManager->addUniqueJob($job);
        */
    }
}
