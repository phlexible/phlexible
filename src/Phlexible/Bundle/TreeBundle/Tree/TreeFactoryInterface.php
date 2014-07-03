<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Tree;

/**
 * Database tree
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface TreeFactoryInterface
{
    /**
     * @param string $siterootId
     *
     * @return TreeInterface
     */
    public function factory($siterootId);
}
