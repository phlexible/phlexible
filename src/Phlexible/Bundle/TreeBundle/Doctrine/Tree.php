<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Doctrine;

use Doctrine\DBAL\Connection;
use Phlexible\Bundle\ElementBundle\Model\ElementHistoryManagerInterface;
use Phlexible\Bundle\TreeBundle\Event\MoveNodeEvent;
use Phlexible\Bundle\TreeBundle\Event\NodeEvent;
use Phlexible\Bundle\TreeBundle\Event\ReorderNodeEvent;
use Phlexible\Bundle\TreeBundle\Exception\InvalidNodeMoveException;
use Phlexible\Bundle\TreeBundle\Model\StateManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeIdentifier;
use Phlexible\Bundle\TreeBundle\Model\TreeInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeNode;
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
class Tree implements TreeInterface, WritableTreeInterface, \IteratorAggregate, IdentifiableInterface
{
    /**
     * @var string
     */
    private $siterootId;

    /**
     * @var array
     */
    private $nodes = array();

    /**
     * @var array
     */
    private $childNodes = array();

    /**
     * @var Connection
     */
    private $connection;

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
     * @param Connection                     $connection
     * @param ElementHistoryManagerInterface $historyManager
     * @param StateManagerInterface          $stateManager
     * @param EventDispatcherInterface       $dispatcher
     */
    public function __construct(
        $siterootId,
        Connection $connection,
        ElementHistoryManagerInterface $historyManager,
        StateManagerInterface $stateManager,
        EventDispatcherInterface $dispatcher)
    {
        $this->siterootId = $siterootId;
        $this->connection = $connection;
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
     * @param array $rows
     *
     * @return TreeNodeInterface[]
     */
    private function mapNodes(array $rows)
    {
        $nodes = array();
        foreach ($rows as $row) {
            $node = $this->mapNode($row);
            $nodes[$node->getId()] = $node;
        }

        return $nodes;
    }

    /**
     * @param array $row
     *
     * @return TreeNodeInterface
     */
    private function mapNode(array $row)
    {
        $attributes = json_decode($row['attributes'], true) ? : array();

        $node = new TreeNode();
        $node
            ->setTree($this)
            ->setId($row['id'])
            ->setParentId($row['parent_id'])
            ->setType($row['type'])
            ->setTypeId($row['type_id'])
            ->setSort($row['sort'])
            ->setSortMode($row['sort_mode'])
            ->setSortDir($row['sort_dir'])
            ->setInNavigation(!!$row['in_navigation'])
            ->setAttributes($attributes)
            ->setCreatedAt(new \DateTime($row['created_at']))
            ->setCreateUserId($row['create_user_id']);

        $this->nodes[$node->getId()] = $node;

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoot()
    {
        if (isset($this->nodes[null])) {
            return $this->nodes[null];
        }

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('et.*')
            ->from('tree', 'et')
            ->where($qb->expr()->isNull('et.parent_id'))
            ->andWhere($qb->expr()->eq('et.siteroot_id', $qb->expr()->literal($this->siterootId)));

        $row = $this->connection->fetchAssoc($qb->getSQL());

        if (!$row) {
            throw new \Exception("Root node for tree {$this->siterootId} not found.");
        }

        return $this->mapNode($row);
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        if (isset($this->nodes[$id])) {
            return $this->nodes[$id];
        }

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('et.*')
            ->from('tree', 'et')
            ->where($qb->expr()->eq('et.id', $id))
            ->andWhere($qb->expr()->eq('et.siteroot_id', $qb->expr()->literal($this->siterootId)));

        $row = $this->connection->fetchAssoc($qb->getSQL());

        if (!$row) {
            throw new \Exception("$id not found");
        }

        return $this->mapNode($row);
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        if (isset($this->nodes[$id])) {
            return $this->nodes[$id];
        }

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('et.*')
            ->from('tree', 'et')
            ->where($qb->expr()->eq('et.id', $id))
            ->andWhere($qb->expr()->eq('et.siteroot_id', $qb->expr()->literal($this->siterootId)));

        $row = $this->connection->fetchAssoc($qb->getSQL());

        if (!$row) {
            return false;
        }

        $this->mapNode($row);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren($node)
    {
        if ($node instanceof TreeNodeInterface) {
            $id = $node->getId();
        } else {
            $id = $node;
        }

        if (isset($this->childNodes[$id])) {
            return $this->childNodes[$id];
        }

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('et.*')
            ->from('tree', 'et')
            ->where($qb->expr()->eq('et.parent_id', $id))
            ->andWhere($qb->expr()->eq('et.siteroot_id', $qb->expr()->literal($this->siterootId)))
            ->orderBy('sort', 'ASC');

        $rows = $this->connection->fetchAll($qb->getSQL());

        $childNodes = $this->mapNodes($rows);
        $this->childNodes[$id] = $childNodes;

        return $childNodes;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildren($node)
    {
        return count($this->getChildren($node)) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent($node)
    {
        if (!$node instanceof TreeNodeInterface) {
            $node = $this->get($node);
        }

        $parentId = $node->getParentId();

        if ($parentId === null) {
            return null;
        }

        return $this->get($parentId);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdPath($node)
    {
        return array_keys($this->getPath($node));
    }

    /**
     * {@inheritdoc}
     */
    public function getPath($node)
    {
        if (!$node instanceof TreeNodeInterface) {
            $node = $this->get($node);
        }

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
    public function isRoot($node)
    {
        if ($node instanceof TreeNodeInterface) {
            $id = $node->getId();
        } else {
            $id = $node;
        }

        return $this->getRoot()->getId() === $id;
    }

    /**
     * {@inheritdoc}
     */
    public function isChildOf($childId, $parentId)
    {
        if ($parentId instanceof TreeNodeInterface) {
            $parentId = $parentId->getId();
        }

        $path = $this->getIdPath($childId);

        foreach ($path as $id) {
            if ($parentId === $id) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isParentOf($parentId, $childId)
    {
        return $this->isChildOf($childId, $parentId);
    }

    /**
     * {@inheritdoc}
     */
    public function isInstance($node)
    {
        if (!$node instanceof TreeNodeInterface) {
            $node = $this->get($node);
        }

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('COUNT(t.id)')
            ->from('tree', 't')
            ->where($qb->expr()->eq('t.type', $qb->expr()->literal($node->getType())))
            ->andWhere($qb->expr()->eq('t.type_id', $node->getTypeId()));

        return $this->connection->fetchColumn($qb->getSQL()) > 1;
    }

    /**
     * {@inheritdoc}
     */
    public function isInstanceMaster($node)
    {
        return $node->getAttribute('instanceMaster', false);
    }

    /**
     * {@inheritdoc}
     */
    public function getInstances($node)
    {
        if (!$node instanceof TreeNodeInterface) {
            $node = $this->get($node);
        }

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('t.*')
            ->from('tree', 't')
            ->where($qb->expr()->eq('t.type_id', $node->getTypeId()))
            ->andWhere($qb->expr()->eq('t.siteroot_id', $qb->expr()->literal($this->getSiterootId())));

        $rows = $this->connection->fetchAll($qb->getSQL());

        return $this->mapNodes($rows);
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
    public function init($type, $typeId, $userId)
    {
        $node = new TreeNode();
        $node
            ->setTree($this)
            ->setParentId(null)
            ->setType($type)
            ->setTypeId($typeId)
            ->setCreateUserId($userId)
            ->setCreatedAt(new \DateTime);

        $event = new NodeEvent($node);
        if ($this->dispatcher->dispatch(TreeEvents::BEFORE_CREATE_NODE, $event)->isPropagationStopped()) {
            return false;
        }

        $this->insertNode($node);

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
        $parentNode,
        $afterNode = null,
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
            ->setParentId($parentNode->getId())
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

        $this->insertNode($node);

        foreach ($sortNodes as $sortNode) {
            $this->updateNode($sortNode);
        }

        // history
        $this->historyManager->insert(ElementHistoryManagerInterface::ACTION_CREATE_NODE, $typeId, $userId, $node->getId());

        $event = new NodeEvent($node);
        $this->dispatcher->dispatch(TreeEvents::CREATE_NODE, $event);

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    public function createInstance($parentNode, $afterNode = null, $sourceNode, $userId) {

        if (!$parentNode instanceof TreeNodeInterface) {
            $parentNode = $this->get($parentNode);
        }
        if ($afterNode && !$afterNode instanceof TreeNodeInterface) {
            $afterNode = $this->get($afterNode);
        }
        if (!$sourceNode instanceof TreeNodeInterface) {
            $sourceNode = $this->get($sourceNode);
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
            ->setParentId($parentNode->getId())
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

        $this->insertNode($node);

        foreach ($sortNodes as $sortNode) {
            $this->updateNode($sortNode);
        }

        // history
        $this->historyManager->insert(ElementHistoryManagerInterface::ACTION_CREATE_NODE_INSTANCE, $node->getTypeId(), $userId, $node->getId());

        $event = new NodeEvent($node);
        $this->dispatcher->dispatch(TreeEvents::CREATE_NODE_INSTANCE, $event);

        return $node;
    }

    /**
     * @param TreeNodeInterface $node
     */
    private function insertNode(TreeNodeInterface $node)
    {
        $this->connection->insert(
            'tree',
            array(
                'siteroot_id'    => $node->getTree()->getSiterootId(),
                'parent_id'      => $node->getParentId(),
                'type'           => $node->getType(),
                'type_id'        => $node->getTypeId(),
                'sort'           => $node->getSort(),
                'sort_mode'      => $node->getSortMode(),
                'sort_dir'       => $node->getSortDir(),
                'in_navigation'  => $node->getInNavigation(),
                'attributes'     => json_encode($node->getAttributes()),
                'created_at'     => $node->getCreatedAt()->format('Y-m-d H:i:s'),
                'create_user_id' => $node->getCreateUserId(),
            )
        );

        $node->setId($this->connection->lastInsertId('tree'));
    }

    /**
     * @param TreeNodeInterface $node
     */
    public function updateNode(TreeNodeInterface $node)
    {
        $this->connection->update(
            'tree',
            array(
                'siteroot_id'    => $node->getTree()->getSiterootId(),
                'parent_id'      => $node->getParentId(),
                'type'           => $node->getType(),
                'type_id'        => $node->getTypeId(),
                'sort'           => $node->getSort(),
                'sort_mode'      => $node->getSortMode(),
                'sort_dir'       => $node->getSortDir(),
                'in_navigation'  => $node->getInNavigation(),
                'attributes'     => json_encode($node->getAttributes()),
                'created_at'     => $node->getCreatedAt()->format('Y-m-d H:i:s'),
                'create_user_id' => $node->getCreateUserId(),
            ),
            array(
                'id' => $node->getId(),
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function reorder($node, $targetNode, $before = false)
    {
        if (!$node instanceof TreeNodeInterface) {
            $node = $this->get($node);
        }

        if (!$targetNode instanceof TreeNodeInterface) {
            $targetNode = $this->get($targetNode);
        }

        $parentNode = $this->getParent($node);
        if ($targetNode->getParentId() !== $parentNode->getId()) {
            throw new InvalidNodeMoveException('Node and targetNode need to have the same parent.');
        }

        if ($parentNode->getSortMode() !== 'free') {
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

        foreach ($this->getChildren($parentNode) as $childNode) {
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
    public function move($node, $toNode, $userId)
    {
        if (!$node instanceof TreeNodeInterface) {
            $node = $this->get($node);
        }

        if (!$toNode instanceof TreeNodeInterface) {
            $toNode = $this->get($toNode);
        }

        if ($this->isChildOf($toNode, $node)) {
            throw new InvalidNodeMoveException('Invalid move.');
        }

        $oldParentId = $node->getParentId();

        $event = new MoveNodeEvent($node, $toNode);
        if ($this->dispatcher->dispatch(TreeEvents::BEFORE_MOVE_NODE, $event)->isPropagationStopped()) {
            return;
        }

        $node
            ->setSort(0)
            ->setParentId($toNode->getId());

        $this->connection->update(
            'tree',
            array(
                'parent_id' => $toNode->getId(),
                'sort'      => 0,
            ),
            array(
                'id' => $node->getId()
            )
        );

        $this->sorter->sortNode($toNode);

        $event = new MoveNodeEvent($node, $toNode);
        $this->dispatcher->dispatch(TreeEvents::MOVE_NODE, $event);

        // history
        $this->historyManager->insert(ElementHistoryManagerInterface::ACTION_MOVE_NODE, $node->getTypeId(), $userId, $node->getId(), null, null, null, 'Node moved from TID ' . $oldParentId . ' to TID ' . $toNode->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function delete($node, $userId, $comment = null)
    {
        if (!$node instanceof TreeNodeInterface) {
            $node = $this->get($node);
        }

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
            $this->doDelete($childNode, $uid, $comment);
        }

        $event = new NodeEvent($node);
        if ($this->dispatcher->dispatch(TreeEvents::BEFORE_DELETE_NODE, $event)->isPropagationStopped()) {
            return;
        }

        $id = $node->getId();

        $this->connection->delete(
            'tree',
            array(
                'id' => $id
            )
        );

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
