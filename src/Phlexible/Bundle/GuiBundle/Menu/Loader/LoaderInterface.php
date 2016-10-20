<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Menu\Loader;

use Phlexible\Bundle\GuiBundle\Menu\MenuItemCollection;

/**
 * Loader interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface LoaderInterface
{
    /**
     * Load config file
     *
     * @param string $file
     *
     * @return MenuItemCollection
     */
    public function load($file);

    /**
     * Is the config file supported?
     *
     * @param string $file
     *
     * @return bool
     */
    public function supports($file);
}
