<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\ElementBundle\Model\ElementHistoryManagerInterface;
use Phlexible\Bundle\TreeBundle\Event\MoveNodeEvent;
use Phlexible\Bundle\TreeBundle\Event\NodeEvent;
use Phlexible\Bundle\TreeBundle\Event\PublishNodeEvent;
use Phlexible\Bundle\TreeBundle\Event\ReorderNodeEvent;
use Phlexible\Bundle\TreeBundle\Event\SetNodeOfflineEvent;
use Phlexible\Bundle\TreeBundle\Exception\InvalidNodeMoveException;
use Phlexible\Bundle\TreeBundle\Model\StateManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeIdentifier;
use Phlexible\Bundle\TreeBundle\Model\TreeInterface;
use Phlexible\Bundle\TreeBundle\Entity\TreeNode;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Phlexible\Bundle\TreeBundle\Model\WritableTreeInterface;
use Phlexible\Bundle\TreeBundle\Tree\TreeIterator;
use Phlexible\Bundle\TreeBundle\TreeEvents;
use Phlexible\Component\Identifier\IdentifiableInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Database tree
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Tree implements TreeInterface, WritableTreeInterface, IdentifiableInterface
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
     */
    public function __construct(
        $siterootId,
        EntityManager $entityManager,
        ElementHistoryManagerInterface $historyManager,
        StateManagerInterface $stateManager,
        EventDispatcherInterface $dispatcher)
    {
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
     *
     * @return TreeIdentifier
     */
    public function getIdentifier()
    {
        return new TreeIdentifier($this->siterootId);
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
        $node = $this->getTreeNodeRepository()->findOneBy(array('siterootId' => $this->siterootId, 'parentNode' => null));
        $node->setTree($this);

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $node = $this->getTreeNodeRepository()->findOneBy(array('siterootId' => $this->siterootId, 'id' => $id));
        if ($node) {
            $node->setTree($this);
        }

        return $node;
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
    public function getChildren(TreeNodeInterface $node)
    {
        $nodes = $this->getTreeNodeRepository()->findBy(array('siterootId' => $this->siterootId, 'parentNode' => $node->getId()), array('sort' => 'ASC'));
        foreach ($nodes as $node) {
            $node->setTree($this);
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
        $ids = array();
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
        $path = array();

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
        $nodes = $this->getTreeNodeRepository()->findBy(array('siterootId' => $this->siterootId, 'type' => $node->getType(), 'typeId' => $node->getTypeId()));
        foreach ($nodes as $node) {
            $node->setTree($this);
        }

        return $nodes;
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
        $sortNodes = array();
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

        $this->updateNode($node, false);

        foreach ($sortNodes as $sortNode) {
            $this->updateNode($sortNode, false);
        }

        $this->entityManager->flush();

        // history
        $this->historyManager->insert(ElementHistoryManagerInterface::ACTION_CREATE_NODE, $typeId, $userId, $node->getId());

        $event = new NodeEvent($node);
        $this->dispatcher->dispatch(TreeEvents::CREATE_NODE, $event);

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    public function createInstance(TreeNodeInterface $parentNode, TreeNodeInterface $afterNode = null, TreeNodeInterface $sourceNode, $userId) {

        $sort = 0;
        $sortNodes = array();
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

        $this->updateNode($node, false);

        foreach ($sortNodes as $sortNode) {
            $this->updateNode($sortNode, false);
        }

        $this->entityManager->flush();

        // history
        $this->historyManager->insert(ElementHistoryManagerInterface::ACTION_CREATE_NODE_INSTANCE, $node->getTypeId(), $userId, $node->getId());

        $event = new NodeEvent($node);
        $this->dispatcher->dispatch(TreeEvents::CREATE_NODE_INSTANCE, $event);

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    public function reorder(TreeNodeInterface $node, TreeNodeInterface $targetNode, $before = false)
    {
        if ($targetNode->getParentNode()->getId() !== $node->getParentNode()->getId()) {
            throw new InvalidNodeMoveException('Node and targetNode need to have the same parent.');
        }

        if ($node->getParentNode()->getSortMode() !== 'free') {
            return;
        }

        $event = new ReorderNodeEvent($node, $targetNode, $before);
        if ($this->dispatcher->dispatch(TreeEvents::BEFORE_REORDER_NODE, $event)->isPropagationStopped()) {
            return;
        }

        if ($before) {
            $sort = $targetNode->getSort();
        } else {
            $sort = $targetNode->getSort() + 1;
        }

        $updatesNodes = array();

        foreach ($this->getChildren($node->getParentNode()) as $childNode) {
            if ($childNode->getSort() <= $sort) {
                $childNode->setSort($childNode->getSort() + 1);
                $updatesNodes[] = $childNode;
            }
        }

        $node->setSort($sort);
        $updateNodes[] = $node;

        foreach ($updateNodes as $updateNode) {
            $this->updateNode($updateNode);
        }

        $event = new ReorderNodeEvent($node, $targetNode, $before);
        $this->dispatcher->dispatch(TreeEvents::REORDER_NODE, $event);

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

        $this->sorter->sortNode($toNode);

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
    }

    protected function _deleteCheck(Makeweb_Elements_Tree_Node $node, array $rightsIdentifiers)
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
            $this->_deleteCheck($childNode, $rightsIdentifiers);
        }
    }

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

        $event = new NodeEvent($node);
        if ($this->dispatcher->dispatch(TreeEvents::BEFORE_DELETE_NODE, $event)->isPropagationStopped()) {
            return;
        }

        $this->entityManager->remove($node);
        $this->entityManager->flush();

        $event = new NodeEvent($node);
        $this->dispatcher->dispatch(TreeEvents::DELETE_NODE, $event);

        // history
        $this->historyManager->insert(ElementHistoryManagerInterface::ACTION_MOVE_NODE, $node->getTypeId(), $userId, $node->getId(), null, null, null, $comment);

        // TODO: -> elements, listener
        /*
        $queueManager = $container->queueManager;
        $job = new Makeweb_Elements_Job_UpdateUsage();
        $job->setEid($eid);
        $queueManager->addUniqueJob($job);
        */
    }
}
