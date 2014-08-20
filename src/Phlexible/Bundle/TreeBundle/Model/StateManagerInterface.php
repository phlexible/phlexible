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
     * @param TreeNodeInterface|int $node
     * @param string                $language
     *
     * @return bool
     */
    public function isPublished($node, $language);

    /**
     * @param TreeNodeInterface|int $node
     *
     * @return array
     */
    public function getPublishedLanguages($node);

    /**
     * @param TreeNodeInterface|int $node
     *
     * @return array
     */
    public function getPublishedVersions($node);

    /**
     * @param TreeNodeInterface|int $node
     * @param string                $language
     *
     * @return int
     */
    public function getPublishedVersion($node, $language);

    /**
     * @param TreeNodeInterface|int $node
     * @param string                $language
     *
     * @return array
     */
    public function getPublishInfo($node, $language);

    /**
     * @param TreeNodeInterface|int $node
     * @param string                $language
     *
     * @return bool
     */
    public function isAsync($node, $language);
}
