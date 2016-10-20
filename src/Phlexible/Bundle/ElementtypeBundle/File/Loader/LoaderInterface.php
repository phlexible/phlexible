<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementtypeBundle\File\Loader;

use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;

/**
 * Loader interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface LoaderInterface
{
    /**
     * @param string $elementtypeId
     *
     * @return Elementtype
     */
    public function load($elementtypeId);

    /**
     * @return Elementtype[]
     */
    public function loadAll();
}
