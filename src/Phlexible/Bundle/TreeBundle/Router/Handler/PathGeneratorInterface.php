<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Router\Handler;

use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;

/**
 * Path generator interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface PathGeneratorInterface
{
    /**
     * Generate path
     *
     * @param TreeNodeInterface $node
     * @param array             $parameters
     *
     * @return string
     */
    public function generatePath(TreeNodeInterface $node, $parameters);
}
