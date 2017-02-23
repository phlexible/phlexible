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

/**
 * Root container.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RootContainer extends AbstractContainer
{
    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return 'p-elementtype-container_root-icon';
    }
}
