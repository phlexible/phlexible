<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Model;

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
