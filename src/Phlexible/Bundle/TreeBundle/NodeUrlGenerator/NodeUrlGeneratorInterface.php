<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\NodeUrlGenerator;

use Phlexible\Bundle\TreeBundle\Entity\TreeNode;

/**
 * Node url generator interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface NodeUrlGeneratorInterface
{
    /**
     * @param TreeNode $node
     * @param string   $language
     *
     * @return string
     */
    public function generatePreviewUrl(TreeNode $node, $language);

    /**
     * @param TreeNode $node
     * @param string   $language
     *
     * @return string
     */
    public function generateOnlineUrl(TreeNode $node, $language);
}
