<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Asset\Builder;

use Doctrine\Common\Collections\ArrayCollection;
use Phlexible\Bundle\GuiBundle\Compressor\JavascriptCompressor\JavascriptCompressorInterface;
use Puli\Repository\Api\ResourceRepository;
use Puli\Repository\Resource\DirectoryResource;
use Puli\Repository\Resource\FileResource;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Scripts builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ScriptsBuilder
{
    /**
     * @var ResourceRepository
     */
    private $puliRepository;

    /**
     * @var JavascriptCompressorInterface
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
     * @param ResourceRepository            $puliRepository
     * @param JavascriptCompressorInterface $compressor
     * @param string                        $cacheDir
     * @param bool                          $debug
     */
    public function __construct(
        ResourceRepository $puliRepository,
        JavascriptCompressorInterface $compressor,
        $cacheDir,
        $debug)
    {
        $this->puliRepository = $puliRepository;
        $this->compressor = $compressor;
        $this->cacheDir = $cacheDir;
        $this->debug = $debug;
    }

    /**
     * Get all javascripts for the given section
     *
     * @return string
     */
    public function get()
    {
        $cacheFilename = $this->cacheDir . '/gui.js';
        $filesystem = new Filesystem();
        if ($filesystem->exists($cacheFilename) && !$this->debug) {
            return file_get_contents($cacheFilename);
        }

        $entryPoints = array();

        $dir = $this->puliRepository->get('/phlexible/scripts');
        /* @var $dir DirectoryResource */
        foreach ($dir->listChildren() as $dir) {
            foreach ($dir->listChildren() as $resource) {
                if ($resource instanceof FileResource && substr($resource->getName(), -3) === '.js') {
                    $entryPoints[$resource->getPath()] = $resource->getFilesystemPath();
                }
            }
        }

        $files = array();
        foreach ($this->puliRepository->find('/phlexible/scripts/*/*.js') as $resource) {
            /* @var $resource FileResource */

            $body = $resource->getBody();

            $file = new \stdClass();
            $file->path = $resource->getPath();
            $file->file = $resource->getFilesystemPath();
            $file->requires = array();
            $file->provides = array();

            preg_match_all('/Ext\.provide\(["\'](.+)["\']\)/', $body, $matches);
            foreach ($matches[1] as $provide) {
                $file->provides[] = $provide;
            }

            preg_match_all('/Ext\.require\(["\'](.+)["\']\)/', $body, $matches);
            foreach ($matches[1] as $require) {
                $file->requires[] = $require;
            }

            $files[$resource->getPath()] = $file;
        }

        $entryPointFiles = array_intersect_key($files, $entryPoints);

        $symbols = array();
        foreach ($files as $file) {
            foreach ($file->provides as $provide) {
                $symbols[$provide] = $file;
            }
        }

        $results = new ArrayCollection();

        function addToResult($file, ArrayCollection $results, array $symbols)
        {
            if (!empty($file->added)) {
                return;
            }

            $file->added = true;

            if (!empty($file->requires)) {
                foreach ($file->requires as $require) {
                    if (!isset($symbols[$require])) {
                        throw new \Exception("Symbol '$require' not found for file {$file->file}.");
                    }
                    addToResult($symbols[$require], $results, $symbols);
                }
            }

            $results->set($file->path, $file->file);
        };

        foreach ($entryPointFiles as $file) {
        //foreach ($files as $file) {
            addToResult($file, $results, $symbols);
        }

        $unusedPaths = array();
        foreach ($files as $path => $file) {
            if (empty($file->added)) {
                $unusedPaths[] = $path;
            }
        }

        $scripts = '/* Created: ' . date('Y-m-d H:i:s');
        $scripts .= PHP_EOL . ' * ' . PHP_EOL . ' * Unused paths:' . PHP_EOL . ' * ' . implode(PHP_EOL . ' * ', $unusedPaths) . PHP_EOL;
        $scripts .= ' */';
        foreach ($results as $path => $file) {
            if ($this->debug) {
                $scripts .= PHP_EOL . "/* Resource: $path */" . PHP_EOL;
            }
            $scripts .= file_get_contents($file);
        }

        $filesystem->dumpFile($cacheFilename, $scripts);

        if (!$this->debug) {
            $this->compressor->compressFile($cacheFilename);
        }

        return $scripts;
    }
}
