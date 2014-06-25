<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Compressor\CssCompressor;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

/**
 * Simple CSS compressor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SimpleCssCompressor extends AbstractCssCompressor implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function compressString($string)
    {
        return $string;
    }

    /**
     * {@inheritdoc}
     */
    public function filterLoad(AssetInterface $asset)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function filterDump(AssetInterface $asset)
    {
        $asset->setContent($this->compressString($asset->getContent()));
    }
}
