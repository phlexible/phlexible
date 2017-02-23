<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementtypeBundle\Field\Container;

use Phlexible\Bundle\ElementtypeBundle\Field\Field;

/**
 * Abstract container.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class AbstractContainer extends Field
{
    /**
     * {@inheritdoc}
     */
    public function isContainer()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isField()
    {
        return false;
    }
}
