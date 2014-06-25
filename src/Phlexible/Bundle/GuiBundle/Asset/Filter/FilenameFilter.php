<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Asset\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

/**
 * Add filename to asset
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FilenameFilter implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function filterLoad(AssetInterface $asset)
    {
        $content = $asset->getContent();
        $content = '/* File: ' . $asset->getSourceRoot() . '/' . $asset->getSourcePath() . ' */' . PHP_EOL . $content;
        $asset->setContent($content);
    }

    /**
     * {@inheritdoc}
     */
    public function filterDump(AssetInterface $asset)
    {
    }
}
