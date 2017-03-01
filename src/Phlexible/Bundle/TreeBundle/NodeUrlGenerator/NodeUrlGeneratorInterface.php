<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\NodeUrlGenerator;

use Phlexible\Bundle\TreeBundle\Entity\TreeNode;

/**
 * Node url generator interface.
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
