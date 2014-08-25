<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\ContentTree;

use Phlexible\Bundle\SiterootBundle\Entity\Navigation;
use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Phlexible\Bundle\SiterootBundle\Entity\Url;
use Phlexible\Bundle\TreeBundle\Model\TreeInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Content tree interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ContentTreeInterface extends TreeInterface
{
    /**
     * @return Siteroot
     */
    public function getSiteroot();

    /**
     * @return bool
     */
    public function isDefaultSiteroot();

    /**
     * @return Url[]
     */
    public function getUrls();

    /**
     * @return Url
     */
    public function getDefaultUrl();

    /**
     * @return Navigation[]
     */
    public function getNavigations();

    /**
     * @param string $language
     *
     * @return array
     */
    public function getSpecialTids($language = null);

    /**
     * @param TreeNodeInterface $treeNode
     *
     * @return ContentTreeNode
     */
    public function createContentTreeNodeFromTreeNode(TreeNodeInterface $treeNode);

    /**
     * @param TreeNodeInterface|int $node
     *
     * @return array
     */
    public function getLanguages($node);

    /**
     * @param \Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface|int $node
     *
     * @return array
     */
    public function getVersions($node);

    /**
     * @param \Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface|int $node
     * @param string                $language
     *
     * @throws \Exception
     * @return int
     */
    public function getVersion($node, $language);
}
