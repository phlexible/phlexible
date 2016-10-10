<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
