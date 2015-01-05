<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Asset\Builder;

use Phlexible\Bundle\GuiBundle\Compressor\JavascriptCompressor\JavascriptCompressorInterface;
use Puli\PuliFactory;
use Puli\Repository\Resource\FileResource;
use Symfony\Component\Yaml\Yaml;

/**
 * Scripts builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ScriptsBuilder
{
    /**
     * @var PuliFactory
     */
    private $puliFactory;

    /**
     * @var JavascriptCompressorInterface
     */
    private $javascriptCompressor;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @param PuliFactory                   $puliFactory
     * @param JavascriptCompressorInterface $javascriptCompressor
     * @param string                        $cacheDir
     * @param bool                          $debug
     */
    public function __construct(
        PuliFactory $puliFactory,
        JavascriptCompressorInterface $javascriptCompressor,
        $cacheDir,
        $debug)
    {
        $this->puliFactory = $puliFactory;
        $this->javascriptCompressor = $javascriptCompressor;
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
        $requires = [];
        $input = [];
        $parser = new Yaml();

        $repo = $this->puliFactory->createRepository();

        foreach ($repo->find('/phlexible/scripts-ux/*/require.yml') as $resource) {
            /* @var $resource FileResource */

            $body = $resource->getBody();
            $config = $parser->parse($body);
            $priority = isset($config['priority']) ? (int) $config['priority'] : 0;
            $priority += 1000;

            if (!isset($config['require'])) {
                die('gna');
            }

            $requires[$priority][] = array(
                'path'     => dirname($resource->getPath()),
                'priority' => $priority,
                'requires' => $config['require'],
            );
        }

        foreach ($repo->find('/phlexible/scripts/*/require.yml') as $resource) {
            /* @var $resource FileResource */

            $body = $resource->getBody();
            $config = $parser->parse($body);
            $priority = isset($config['priority']) ? (int) $config['priority'] : 0;

            if (!isset($config['require'])) {
                die('gna');
            }

            $requires[$priority][] = array(
                'path'     => dirname($resource->getPath()),
                'priority' => $priority,
                'requires' => $config['require'],
            );
        }

        krsort($requires);
        $sortedRequires = [];
        foreach ($requires as $priority => $priorityRequires) {
            $sortedRequires = array_merge($sortedRequires, $priorityRequires);
        }

        foreach ($sortedRequires as $require) {
            /* @var $require FileResource */

            $path = $require['path'];

            if (!isset($require['requires']) || !is_array($require['requires'])) {
                print_r($require);die;
            }
            foreach ($require['requires'] as $file) {
                $input[] = $repo->get("$path/$file.js")->getFilesystemPath();
            }
        }

        $scripts = '';
        foreach ($input as $file) {
            $scripts .= "/* File: $file */" . PHP_EOL;
            $scripts .= file_get_contents($file);
        }

        return $scripts;
    }
}
