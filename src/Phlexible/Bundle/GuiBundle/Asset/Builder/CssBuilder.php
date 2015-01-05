<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Asset\Builder;

use Phlexible\Bundle\GuiBundle\Asset\Filter\BaseUrlFilter;
use Phlexible\Bundle\GuiBundle\Compressor\CssCompressor\CssCompressorInterface;
use Puli\PuliFactory;
use Puli\Repository\Resource\FileResource;

/**
 * CSS builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CssBuilder
{
    /**
     * @var PuliFactory
     */
    private $puliFactory;

    /**
     * @var CssCompressorInterface
     */
    private $cssCompressor;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @param PuliFactory            $puliFactory
     * @param CssCompressorInterface $cssCompressor
     * @param string                 $cacheDir
     * @param bool                   $debug
     */
    public function __construct(
        PuliFactory $puliFactory,
        CssCompressorInterface $cssCompressor,
        $cacheDir,
        $debug)
    {
        $this->puliFactory = $puliFactory;
        $this->cssCompressor = $cssCompressor;
        $this->cacheDir = $cacheDir;
        $this->debug = $debug;
    }

    /**
     * Build stream
     *
     * @param string $baseUrl
     * @param string $basePath
     *
     * @return string
     */
    public function get($baseUrl, $basePath)
    {
        /*
        if (!$this->debug) {
            $filters[] = 'compressor';
            //$filters[] = new Assetic\Filter\Yui\JsCompressorFilter('/Users/swentz/Sites/ofcs/hoffmann/app/Resources/java/yuicompressor-2.4.7.jar');
            //$filters[] = new Assetic\Filter\CssMinFilter();
        }
        */

        $input = [];

        $repo = $this->puliFactory->createRepository();

        foreach ($repo->find('/phlexible/styles/*/*.css') as $resource) {
            /* @var $resource FileResource */
            $input[] = $resource->getFilesystemPath();
        }

        $css = '';
        foreach ($input as $file) {
            $css .= "/* $file */";
            $css .= file_get_contents($file);
        }

        $filter = new BaseUrlFilter($baseUrl, $basePath);
        $css = $filter->filter($css);

        return $css;
    }
}
