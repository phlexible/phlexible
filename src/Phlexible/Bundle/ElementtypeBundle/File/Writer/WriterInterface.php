<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementtypeBundle\File\Writer;

use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;

/**
 * Writer interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface WriterInterface
{
    /**
     * @param Elementtype $elementtype
     *
     * @return string
     */
    public function write(Elementtype $elementtype);
}
