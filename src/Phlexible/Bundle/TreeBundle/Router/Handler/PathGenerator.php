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
 * Path generator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PathGenerator implements PathGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generatePath(TreeNodeInterface $node, $parameters)
    {
        $tree = $node->getTree();

        // we reverse the order to determine if this leaf is no full element
        // if the is the case we don't have to continue, only full elements
        // have paths
        $pathNodes = array_reverse($tree->getPath($node));

        $parts = [];

        foreach ($pathNodes as $pathNode) {
            if ($tree->isSluggable($pathNode)) {
                $part = $pathNode->getSlug($parameters['_locale']);
                if ($part) {
                    $parts[] = $part;
                }
            }
        }

        if (!count($parts)) {
            if (!count($pathNodes)) {
                return '';
            }

            $current = $pathNodes[0];
            $part = $current->getSlug($parameters['_locale']);
            if ($part) {
                $parts[] = $part;
            }
        }

        $path = '/' . implode('/', array_reverse($parts));

        return $path;
    }
}
