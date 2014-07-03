<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Tree;

use Phlexible\Bundle\TreeBundle\Event\BeforeCreateNodeEvent;
use Phlexible\Bundle\TreeBundle\Event\BeforeDeleteNodeEvent;
use Phlexible\Bundle\TreeBundle\Event\BeforeMoveNodeEvent;
use Phlexible\Bundle\TreeBundle\Event\CreateNodeEvent;
use Phlexible\Bundle\TreeBundle\Event\DeleteNodeEvent;
use Phlexible\Bundle\TreeBundle\Event\MoveNodeEvent;
use Phlexible\Bundle\TreeBundle\Exception\InvalidNodeMoveException;
use Phlexible\Bundle\TreeBundle\Tree\Node\TreeNode;
use Phlexible\Bundle\TreeBundle\Tree\Node\TreeNodeInterface;
use Phlexible\Component\Identifier\IdentifiableInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Database tree
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DatabaseTree implements TreeInterface, WritableTreeInterface, \IteratorAggregate, IdentifiableInterface
{
    /**
     * @var string
     */
    private $siterootId = null;

    /**
     * @var array
     */
    private $nodes = array();

    /**
     * @var array
     */
    private $childNodes = array();

    /**
     * @var \Zend_Db_Adapter_Abstract
     */
    private $db;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var TreeHistory
     */
    private $history;

    /**
     * @param string                    $siteRootId
     * @param \Zend_Db_Adapter_Abstract $db
     * @param EventDispatcherInterface  $dispatcher
     * @param TreeHistory               $history
     */
    public function __construct(
        $siteRootId,
        \Zend_Db_Adapter_Abstract $db,
        EventDispatcherInterface $dispatcher,
        TreeHistory $history)
    {
        $this->siterootId = $siteRootId;
        $this->db = $db;
        $this->dispatcher = $dispatcher;
        $this->history = $history;
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
            ->setAttributes($attributes)
            ->setSort($row['sort'])
            ->setSortMode($row['sort_mode'])
            ->setSortDir($row['sort_dir'])
            ->setCreatedAt(new \DateTime($row['create_time']))
            ->setCreateUserId($row['create_uid']);

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

        $select = $this->db
            ->select()
            ->from($this->db->prefix . 'tree')
            ->where('parent_id IS NULL');

        $row = $this->db->fetchRow($select);

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

        $select = $this->db
            ->select()
            ->from($this->db->prefix . 'tree')
            ->where('id = ?', $id);

        $row = $this->db->fetchRow($select);

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
            return true;
        }

        if ($id instanceof TreeNodeInterface) {
            $id = $id->getId();
        }

        return $this->get($id) !== null;
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

        $select = $this->db
            ->select()
            ->from($this->db->prefix . 'tree')
            ->where('parent_id = ?', $id)
            ->order('sort ASC');

        $row = $this->db->fetchAll($select);

        $childNodes = $this->mapNodes($row);
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
     * @param TreeNodeInterface|int $node
     *
     * @return array
     */
    public function getLanguages($node)
    {
        if ($node instanceof TreeNodeInterface) {
            $nodeId = $node->getId();
        } else {
            $nodeId = $node;
        }

        $select = $this->db
            ->select()
            ->from($this->db->prefix . 'tree_online', 'language')
            ->where('tree_id = ?', $nodeId);

        $languages = $this->db->fetchCol($select);

        return $languages;
    }

    /**
     * @param TreeNodeInterface|int $node
     *
     * @return array
     */
    public function getVersions($node)
    {
        if ($node instanceof TreeNodeInterface) {
            $nodeId = $node->getId();
        } else {
            $nodeId = $node;
        }

        $select = $this->db
            ->select()
            ->from($this->db->prefix . 'tree_online', array('language', 'version'))
            ->where('tree_id = ?', $nodeId);

        $versions = $this->db->fetchPairs($select);

        return $versions;
    }

    /**
     * @param TreeNodeInterface|int $node
     * @param string                $language
     *
     * @return int
     */
    public function getVersion($node, $language)
    {
        return $this->getVersions($node)[$language];
    }

    /**
     * {@inheritdoc}
     */
    public function add(
        $parentId,
        $afterId,
        $type,
        $typeId,
        array $attributes,
        $uid,
        $sortMode = 'free',
        $sortDir = 'asc')
    {
        $parentNode = $this->get($parentId);

        $sort = 0;
        $sortNodes = array();
        if ($parentNode->getSortMode() === 'free') {
            if ($afterId !== null) {
                $afterNode = $this->get($afterId);
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
            ->setParentId($parentId)
            ->setType($type)
            ->setTypeId($typeId)
            ->setAttributes($attributes)
            ->setSort($sort)
            ->setSortMode($sortMode)
            ->setSortDir($sortDir)
            ->setCreateUserId($uid)
            ->setCreatedAt(new \DateTime);

        $beforeEvent = new BeforeCreateNodeEvent($this, $node);
        if (false === $this->dispatcher->dispatch($beforeEvent)) {
            return false;
        }

        $this->insertNode($node);

        foreach ($sortNodes as $sortNode) {
            $this->updateNode($sortNode);
        }

        // history
        $this->history->insertCreateNode($node, $uid, null);

        $event = new CreateNodeEvent($node);
        $this->dispatcher->dispatch($event);

        return $node;
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

        $beforeEvent = new BeforeReorderNodeEvent($node, $targetNode, $before);
        if (false === $this->dispatcher->dispatch($beforeEvent)) {
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
        $this->dispatcher->dispatch($event);

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function move($node, $toNode, $uid)
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

        $beforeEvent = new BeforeMoveNodeEvent($node, $toNode);
        if (!$this->dispatcher->dispatch($beforeEvent)) {
            return;
        }

        $node
            ->setSort(0)
            ->setParentId($toNode->getId());

        $this->db->update(
            $this->db->prefix . 'element_tree',
            array(
                'parent_id' => $toNode->getId(),
                'sort'      => 0,
            ),
            array(
                'id = ?' => $node->getId()
            )
        );

        $this->sorter->sortNode($toNode);

        $event = new MoveNodeEvent($node, $toNode);
        $this->dispatcher->dispatch($event);

        // history
        $this->history->insertMoveNode(
            $node,
            $uid,
            null,
            null,
            null,
            'Node moved from TID ' . $oldParentId . ' to TID ' . $toNode->getId()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete($node, $uid, $comment = null)
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

        $this->doDelete($node, $uid, $comment);
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
     * @param string            $uid
     * @param string            $comment
     */
    private function doDelete(TreeNodeInterface $node, $uid, $comment = null)
    {
        foreach ($this->getChildren($node) as $childNode) {
            $this->doDelete($childNode, $uid, $comment);
        }

        $beforeEvent = new BeforeDeleteNodeEvent($node);
        if ($this->dispatcher->dispatch($beforeEvent) === false) {
            return;
        }

        $id = $node->getId();

        $this->db->delete(
            $this->db->prefix . 'tree',
            array(
                'id = ?' => $id
            )
        );

        $event = new DeleteNodeEvent($node);
        $this->dispatcher->dispatch($event);

        // history
        $this->history->insertDeleteNode($node, $uid, null, null, null, $comment);

        // TODO: -> elements, listener
        /*
        $queueManager = $container->queueManager;
        $job = new Makeweb_Elements_Job_UpdateUsage();
        $job->setEid($eid);
        $queueManager->addUniqueJob($job);
        */
    }
}
