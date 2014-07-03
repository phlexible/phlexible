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
use Phlexible\Bundle\TreeBundle\Tree\Node\TreeNodeInterface;
use Phlexible\Bundle\TreeBundle\Tree\TreeInterface;
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
     * @return array
     */
    public function getSpecialTids();

    /**
     * @param TreeNodeInterface|int $node
     *
     * @return array
     */
    public function getLanguages($node);

    /**
     * @param TreeNodeInterface|int $node
     *
     * @return array
     */
    public function getVersions($node);

    /**
     * @param TreeNodeInterface|int $node
     * @param string                $language
     *
     * @throws \Exception
     * @return int
     */
    public function getVersion($node, $language);
}
