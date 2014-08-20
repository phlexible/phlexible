<?php

use Doctrine\DBAL\Connection;

class Makeweb_Elements_Publish
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Makeweb_Elements_Tree_Manager
     */
    protected $_treeManager = null;

    /**
     * @var Makeweb_Elements_Element_Version_Manager
     */
    protected $_elementVersionManager = null;

    /**
     * @var Zend_Db_Select
     */
    protected $_versionSelect = null;

    /**
     * @var Zend_Db_Select
     */
    protected $_instanceSelect = null;

    /**
     * @var Zend_Db_Select
     */
    protected $_teaserSelect = null;

    /**
     * @var Zend_Db_Select
     */
    protected $_teaserInstanceSelect = null;

    /**
     * @var Phlexible\Bundle\AccessControlBundle\Rights
     */
    protected $_contentRightsManager = null;

    /**
     * @var MWF_Core_Users_User
     */
    protected $_currentUser = null;

    /**
     * @var array
     */
    protected $_rightsIdentifiers;

    /**
     * Constructor
     *
     * @param Connection                                  $connection
     * @param Makeweb_Elements_Tree_Manager               $treeManager
     * @param Makeweb_Elements_Element_Version_Manager    $elementVersionManager
     * @param Phlexible\Bundle\AccessControlBundle\Rights $contentRightsManager
     * @param MWF_Core_Users_User                         $currentUser
     */
    public function __construct(
        Connection $connection,
        Makeweb_Elements_Tree_Manager $treeManager,
        Makeweb_Elements_Element_Version_Manager $elementVersionManager,
        Phlexible\Bundle\AccessControlBundle\Rights $contentRightsManager,
        MWF_Core_Users_User $currentUser)
    {
        $this->connection = $connection;
        $this->_elementVersionManager = $elementVersionManager;
        $this->_treeManager = $treeManager;
        $this->_contentRightsManager = $contentRightsManager;
        $this->_currentUser = $currentUser;

        $this->_rightsIdentifiers = array(
            array('uid' => $currentUser->getId())
        );
        foreach ($currentUser->getGroups() as $group) {
            $this->_rightsIdentifiers[] = array('gid' => $group->getId());
        }

        $this->_versionSelect = $db->select()
            ->from($db->prefix . 'element', 'latest_version')
            ->where('eid = :eid')
            ->limit(1);

        $this->_instanceSelect = $db->select()
            ->from($db->prefix . 'element_tree', array('id'))
            ->where('eid = :eid')
            ->where('id != :skipTid');

        $this->_teasersSelect = $db->select()
            ->from(array('ett' => $db->prefix . 'element_tree_teasers'))
            ->joinLeft(
                array('etto' => $db->prefix . 'element_tree_teasers_online'),
                'ett.id = etto.teaser_id AND etto.language = :language'
            )
            ->where('ett.tree_id = :tid');

        $this->_teaserSelect = $db->select()
            ->from(array('ett' => $db->prefix . 'element_tree_teasers'))
            ->joinLeft(
                array('etto' => $db->prefix . 'element_tree_teasers_online'),
                'ett.id = etto.teaser_id AND etto.language = :language'
            )
            ->where('ett.id = :teaserId');

        $this->_teaserInstanceSelect = $db->select()
            ->from($db->prefix . 'element_tree_teasers', array('id'))
            ->where('teaser_eid = :eid')
            ->where('id != :skipTeaserId');
    }

    public function getPreview(
        $tid,
        $teaserId,
        $language,
        $version,
        $includeElements,
        $includeElementInstances,
        $includeTeasers,
        $includeTeaserInstances,
        $recursive,
        $onlyOffline,
        $onlyAsync)
    {
        $node = $this->_treeManager->getNodeByNodeId($tid);

        $result = array();

        if ($includeElements) {
            $result = $this->_handleTreeNode(
                $result,
                0,
                implode('/', $node->getPath()),
                $node,
                $version,
                $language,
                $onlyAsync,
                $onlyOffline
            );
        }
        if ($includeTeasers) {
            $result = $this->_getTeaserArray(
                $result,
                0,
                implode('/', $node->getPath()),
                $node,
                $language,
                $onlyAsync,
                $onlyOffline,
                $includeTeaserInstances
            );
        }

        if ($recursive) {
            $iterator = new Makeweb_Elements_Tree_Node_Iterator($node);
            $rii = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);

            foreach ($rii as $childNode) {
                /* @var $childNode Makeweb_Elements_Tree_Node */
                set_time_limit(5);

                if ($includeElements) {
                    $result = $this->_handleTreeNode(
                        $result,
                        $rii->getDepth() + 1,
                        implode('/', $childNode->getPath()),
                        $childNode,
                        null,
                        $language,
                        $onlyAsync,
                        $onlyOffline
                    );
                }
                if ($includeTeasers) {
                    $result = $this->_getTeaserArray(
                        $result,
                        $rii->getDepth() + 1,
                        implode('/', $childNode->getPath()),
                        $childNode,
                        $language,
                        $onlyAsync,
                        $onlyOffline,
                        $includeTeaserInstances
                    );
                }
            }
        }

        foreach ($result as $key => $row) {
            if ($includeElementInstances && 'full_element' === $row['type']) {
                $instanceTids = $this->_db->fetchCol(
                    $this->_instanceSelect,
                    array(
                        'eid'     => $row['eid'],
                        'skipTid' => $row['tid'],
                    )
                );

                foreach ($instanceTids as $instanceTid) {
                    $instanceNode = $this->_treeManager->getNodeByNodeId($instanceTid);

                    $result = $this->_handleTreeNode(
                        $result,
                        $row['depth'],
                        $row['path'],
                        $instanceNode,
                        null,
                        $language,
                        $onlyAsync,
                        $onlyOffline,
                        true
                    );
                }
            } elseif ($includeTeaserInstances && 'part_element' === $row['type']) {
                $instanceTeaserIds = $this->_db->fetchCol(
                    $this->_teaserInstanceSelect,
                    array(
                        'eid'          => $row['eid'],
                        'skipTeaserId' => $row['teaser_id'],
                    )
                );

                foreach ($instanceTeaserIds as $instanceTeaserId) {
                    $teaser = $this->_db->fetchRow(
                        $this->_teaserSelect,
                        array(
                            'teaserId' => $instanceTeaserId,
                            'language' => $language,
                        )
                    );

                    $result = $this->_handleTeaser(
                        $result,
                        $row['depth'],
                        $teaser,
                        $language,
                        $onlyAsync,
                        $onlyOffline,
                        true
                    );
                }
            }
        }

        return array_values($result);
    }

    protected function _handleTreeNode(
        array $result,
        $depth,
        $path,
        Makeweb_Elements_Tree_Node $node,
        $version,
        $language,
        $onlyAsync,
        $onlyOffline,
        $isInstance = false)
    {
        if (array_key_exists('treenode_' . $node->getId(), $result)) {
            return $result;
        }

        $include = true;

        if ($onlyAsync || $onlyOffline) {
            $include = false;

            if ($onlyAsync && $node->isAsync($language)) {
                $include = true;
            }
            if ($onlyOffline && !$node->isPublished($language)) {
                $include = true;
            }
        }
        if (!$this->_currentUser->isGranted(MWF_Core_Acl_Acl::RESOURCE_SUPERADMIN) &&
            !$this->_currentUser->isGranted(MWF_Core_Acl_Acl::RESOURCE_DEVELOPMENT)
        ) {
            $this->_contentRightsManager->calculateRights('internal', $node, $this->_rightsIdentifiers);
            if (!$this->_contentRightsManager->hasRight('PUBLISH', $language)) {
                $include = false;
            }
        }

        if (!$include) {
            return $result;
        }

        if ($version === null) {
            $version = $this->_db->fetchOne($this->_versionSelect, array('eid' => $node->getEid()));
        }

        $elementVersion = $this->_elementVersionManager->get($node->getEid(), $version);

        $result['treenode_' . $node->getId()] = array(
            'type'      => 'full_element',
            'instance'  => $isInstance,
            'depth'     => $depth,
            'path'      => $path . '+' . $language,
            'tid'       => $node->getId(),
            'teaser_id' => null,
            'eid'       => $node->getEid(),
            'version'   => $version,
            'language'  => $language,
            'title'     => $elementVersion->getBackendTitle($language),
            'icon'      => $elementVersion->getIconUrl($node->getIconParams($language)),
        );

        return $result;
    }

    protected function _getTeaserArray(
        array $result,
        $depth,
        $path,
        Makeweb_Elements_Tree_Node $node,
        $language,
        $onlyAsync,
        $onlyOffline,
        $includeTeaserInstances)
    {
        $teaser = null;

        $teasers = $this->_db->fetchAll(
            $this->_teasersSelect,
            array(
                'tid'      => $node->getId(),
                'language' => $language
            )
        );

        foreach ($teasers as $teaser) {
            $result = $this->_handleTeaser($result, $depth, $path, $teaser, $language, $onlyAsync, $onlyOffline);
        }

        return $result;
    }

    protected function _handleTeaser(
        $result,
        $depth,
        $path,
        $teaser,
        $language,
        $onlyAsync,
        $onlyOffline,
        $isInstance = false)
    {
        if ($teaser['type'] !== 'teaser') {
            return $result;
        }

        if (array_key_exists('teaser_' . $teaser['id'], $result)) {
            return $result;
        }

        $version = $this->_db->fetchOne($this->_versionSelect, array('eid' => $teaser['teaser_eid']));

        $isAsync = !!($teaser['version'] && $teaser['version'] != $version);
        $isPublished = !!$teaser['version'];

        $include = true;
        if ($onlyAsync || $onlyOffline) {
            $include = false;

            if ($onlyAsync && $isAsync) {
                $include = true;
            }
            if ($onlyOffline && !$isPublished) {
                $include = true;
            }
        }

        if (!$include) {
            return $result;
        }

        $elementVersion = $this->_elementVersionManager->get($teaser['teaser_eid'], $version);

        $teaserNode = new Makeweb_Teasers_Node($teaser['id']);

        $result['teaser_' . $teaser['id']] = array(
            'type'      => 'part_element',
            'instance'  => $isInstance,
            'depth'     => $depth,
            'path'      => $path . '+' . $language . '+' . $teaser['id'] . '+' . $language,
            'tid'       => null,
            'teaser_id' => $teaser['id'],
            'eid'       => $teaser['teaser_eid'],
            'version'   => $version,
            'language'  => $language,
            'title'     => $elementVersion->getBackendTitle($language),
            'icon'      => $elementVersion->getIconUrl($teaserNode->getIconParams($language)),
        );

        return $result;
    }
}
