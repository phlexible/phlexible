<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.makeweb.de/LICENCE     Dummy Licence
 */

namespace Phlexible\Bundle\GuiBundle\AssetProvider;

use Assetic\Asset\AssetCollection;

/**
 * Asset provider interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface AssetProviderInterface
{
    /**
     * Return UX scripts
     *
     * @return AssetCollection
     */
    public function getUxScriptsCollection();

    /**
     * Return UX css
     *
     * @return AssetCollection
     */
    public function getUxCssCollection();

    /**
     * Return scripts
     *
     * @return AssetCollection
     */
    public function getScriptsCollection();

    /**
     * Return css
     *
     * @return AssetCollection
     */
    public function getCssCollection();
}
