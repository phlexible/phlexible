<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Model;

/**
 * State manager interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface StateManagerInterface
{
    /**
     * @param TreeNodeInterface $node
     * @param string            $language
     *
     * @return bool
     */
    public function isPublished(TreeNodeInterface$node, $language);

    /**
     * @param TreeNodeInterface $node
     *
     * @return array
     */
    public function getPublishedLanguages(TreeNodeInterface $node);

    /**
     * @param TreeNodeInterface $node
     *
     * @return array
     */
    public function getPublishedVersions(TreeNodeInterface $node);

    /**
     * @param TreeNodeInterface $node
     * @param string            $language
     *
     * @return int
     */
    public function getPublishedVersion(TreeNodeInterface $node, $language);

    /**
     * @param TreeNodeInterface $node
     * @param string            $language
     *
     * @return array
     */
    public function getPublishInfo(TreeNodeInterface $node, $language);

    /**
     * @param TreeNodeInterface $node
     * @param string            $language
     *
     * @return bool
     */
    public function isAsync(TreeNodeInterface $node, $language);
}
