<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Menu\Loader;

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