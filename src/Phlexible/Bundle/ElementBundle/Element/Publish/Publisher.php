<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Element\Publish;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\SecurityBundle\Acl\Acl;
use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TeaserBundle\Model\TeaserManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Phlexible\Bundle\TreeBundle\Tree\TreeManager;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Publisher
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Publisher
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
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @param ElementService           $elementService
     * @param TreeManager              $treeManager
     * @param TeaserManagerInterface   $teaserManager
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(
        ElementService $elementService,
        TreeManager $treeManager,
        TeaserManagerInterface $teaserManager,
        SecurityContextInterface $securityContext)
    {
        $this->elementService = $elementService;
        $this->treeManager = $treeManager;
        $this->teaserManager = $teaserManager;
        $this->securityContext = $securityContext;

        /*
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
        */
    }

    /**
     * @param int    $treeId
     * @param string $language
     * @param int    $version
     * @param bool   $includeElements
     * @param bool   $includeElementInstances
     * @param bool   $includeTeasers
     * @param bool   $includeTeaserInstances
     * @param bool   $recursive
     * @param bool   $onlyOffline
     * @param bool   $onlyAsync
     *
     * @return array
     */
    public function getPreview(
        $treeId,
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
        $tree = $this->treeManager->getByNodeId($treeId);
        $treeNode = $tree->get($treeId);

        $result = array();

        if ($includeElements) {
            $result = $this->handleTreeNode(
                $result,
                0,
                implode('/', $treeNode->getPath()),
                $treeNode,
                $version,
                $language,
                $onlyAsync,
                $onlyOffline
            );
        }
        if ($includeTeasers) {
            $result = $this->handleTreeNodeTeasers(
                $result,
                0,
                implode('/', $treeNode->getPath()),
                $treeNode,
                $language,
                $onlyAsync,
                $onlyOffline,
                $includeTeaserInstances
            );
        }

        if ($recursive) {
            $rii = new \RecursiveIteratorIterator($treeNode->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);

            foreach ($rii as $childNode) {
                /* @var $childNode TreeNodeInterface */

                set_time_limit(5);

                if ($includeElements) {
                    $result = $this->handleTreeNode(
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
                    $result = $this->handleTreeNodeTeasers(
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
                $instanceNodes = $this->treeManager->getInstanceNodes($treeNode);

                foreach ($instanceNodes as $instanceNode) {
                    /* @var $instanceNode TreeNodeInterface */

                    $result = $this->handleTreeNode(
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
                $instanceTeasers = $this->teaserManager->getInstances($teaser);

                foreach ($instanceTeasers as $instanceTeaser) {
                    $result = $this->handleTeaser(
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

    private function handleTreeNode(
        array $result,
        $depth,
        $path,
        TreeNodeInterface $treeNode,
        $version,
        $language,
        $onlyAsync,
        $onlyOffline,
        $isInstance = false)
    {
        if (array_key_exists('treenode_' . $treeNode->getId(), $result)) {
            return $result;
        }

        $include = true;

        if ($onlyAsync || $onlyOffline) {
            $include = false;

            if ($onlyAsync && $treeNode->getTree()->isAsync($treeNode, $language)) {
                $include = true;
            }
            if ($onlyOffline && !$treeNode->getTree()->isPublished($treeNode, $language)) {
                $include = true;
            }
        }
        if (!$this->securityContext->isGranted('ROLE_SUPER_ADMIN', $treeNode)) {
            if (!$this->securityContext->isGranted($treeNode, array('right' => 'PUBLISH', 'language' => $language))) {
                $include = false;
            }
        }

        if (!$include) {
            return $result;
        }

        $element = $this->elementService->findElement($treeNode->getTypeId(), $version);
        if ($version) {
            $elementVersion = $this->elementService->findElementVersion($element, $version);
        } else {
            $elementVersion = $this->elementService->findLatestElementVersion($element);
        }

        $result['treenode_' . $treeNode->getId()] = array(
            'type'      => 'full_element',
            'instance'  => $isInstance,
            'depth'     => $depth,
            'path'      => $path . '+' . $language,
            'tid'       => $treeNode->getId(),
            'teaser_id' => null,
            'eid'       => $treeNode->getEid(),
            'version'   => $version,
            'language'  => $language,
            'title'     => $elementVersion->getBackendTitle($language),
            'icon'      => '',// TODO: $elementVersion->getIconUrl($treeNode->getIconParams($language)),
        );

        return $result;
    }

    protected function handleTreeNodeTeasers(
        array $result,
        $depth,
        $path,
        TreeNodeInterface $treeNode,
        $language,
        $onlyAsync,
        $onlyOffline,
        $includeTeaserInstances)
    {
        $teasers = $this->teaserManager->findForLayoutAreaAndTreeNode(null, $treeNode);

        foreach ($teasers as $teaser) {
            $result = $this->handleTeaser($result, $depth, $path, $teaser, $language, $onlyAsync, $onlyOffline);
        }

        return $result;
    }

    protected function handleTeaser(
        $result,
        $depth,
        $path,
        Teaser $teaser,
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

        $elementVersion = $this->elementService->get($teaser['teaser_eid'], $version);

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
