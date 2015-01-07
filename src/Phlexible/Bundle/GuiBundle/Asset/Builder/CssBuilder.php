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
use Puli\Repository\Api\ResourceRepository;
use Puli\Repository\Resource\FileResource;
use Symfony\Component\Filesystem\Filesystem;

/**
 * CSS builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CssBuilder
{
    /**
     * @var ResourceRepository
     */
    private $puliRepository;

    /**
     * @var CssCompressorInterface
     */
    private $compressor;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @param ResourceRepository     $puliRepository
     * @param CssCompressorInterface $compressor
     * @param string                 $cacheDir
     * @param bool                   $debug
     */
    public function __construct(
        ResourceRepository $puliRepository,
        CssCompressorInterface $compressor,
        $cacheDir,
        $debug)
    {
        $this->puliRepository = $puliRepository;
        $this->compressor = $compressor;
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
        $cacheFilename = $this->cacheDir . '/gui.css';
        $filesystem = new Filesystem();
        if ($filesystem->exists($cacheFilename) && !$this->debug) {
            return file_get_contents($cacheFilename);
        }

        $input = [];

        foreach ($this->puliRepository->find('/phlexible/styles/*/*.css') as $resource) {
            /* @var $resource FileResource */
            $input[] = $resource->getFilesystemPath();
        }

        $css = '/* Created: ' . date('Y-m-d H:i:s') . ' */';
        foreach ($input as $file) {
            if ($this->debug) {
                $css .= PHP_EOL . "/* File: $file */" . PHP_EOL;
            }
            $css .= file_get_contents($file);
        }

        $filter = new BaseUrlFilter($baseUrl, $basePath);
        $css = $filter->filter($css);

        $filesystem->dumpFile($cacheFilename, $css);

        if (!$this->debug) {
            $this->compressor->compressFile($cacheFilename);
        }

        return $css;
    }
}
