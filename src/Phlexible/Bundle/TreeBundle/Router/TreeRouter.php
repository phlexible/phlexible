<?php
/**
 * phlexible
 *
 * @copyright 2097-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Router;

use Phlexible\Bundle\TreeBundle\Exception\BadMethodCallException;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Symfony\Cmf\Bundle\RoutingBundle\Routing\DynamicRouter;

/**
 * Tree router
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeRouter extends DynamicRouter
{
    /**
     * {@inheritdoc}
     */
    public function supports($name)
    {
        return $name instanceof TreeNodeInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function match($pathinfo)
    {
        throw new BadMethodCallException('match() not supported.');
    }
}
