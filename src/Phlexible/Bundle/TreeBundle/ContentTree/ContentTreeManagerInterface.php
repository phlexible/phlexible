<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\ContentTree;

/**
 * Content tree manager interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ContentTreeManagerInterface
{
    /**
     * @return ContentTreeInterface[]
     */
    public function findAll();

    /**
     * @param string $siterootId
     *
     * @return ContentTreeInterface
     */
    public function find($siterootId);

    /**
     * @param int $treeId
     *
     * @return ContentTreeInterface|null
     */
    public function findByTreeId($treeId);
}
