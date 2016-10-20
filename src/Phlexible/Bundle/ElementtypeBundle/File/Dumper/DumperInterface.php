<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementtypeBundle\File\Dumper;

use FluentDOM\Document;
use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;

/**
 * Dumper interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface DumperInterface
{
    /**
     * @param Elementtype $elementtype
     *
     * @return Document
     */
    public function dump(Elementtype $elementtype);
}
