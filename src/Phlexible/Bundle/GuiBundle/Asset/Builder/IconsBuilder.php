<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Asset\Builder;

use Phlexible\Bundle\GuiBundle\Compressor\CssCompressor\CssCompressorInterface;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Icons builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class IconsBuilder
{
    /**
     * @var array
     */
    private $bundles;

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
     * @param array                  $bundles
     * @param CssCompressorInterface $cssCompressor
     * @param string                 $cacheDir
     * @param bool                   $debug
     */
    public function __construct(
        array $bundles,
        CssCompressorInterface $cssCompressor,
        $cacheDir,
        $debug)
    {
        $this->bundles = $bundles;
        $this->cssCompressor = $cssCompressor;
        $this->cacheDir = rtrim($cacheDir, '/') . '/';
        $this->debug = $debug;
    }

    /**
     * Generate a cache file name
     *
     * @return string
     */
    public function getCacheFilename()
    {
        $cacheFilename = $this->cacheDir . 'icons.css';

        return $cacheFilename;
    }

    /**
     * Get all Stylesheets for the given section
     *
     * @param string $baseUrl
     * @param string $basePath
     *
     * @return string
     */
    public function get($baseUrl, $basePath)
    {
        $cacheFilename = $this->getCacheFilename();

        $configCache = new ConfigCache($cacheFilename, $this->debug);
        if (!$configCache->isFresh()) {
            $resources = new \ArrayObject();
            $resources[] = new FileResource(__FILE__);
            $content = $this->buildIcons($this->bundles, $baseUrl, $basePath, $resources);

            $configCache->write($content, (array) $resources);
        } else {
            $content = file_get_contents($configCache);
        }

        return $content;
    }

    /**
     * Glue together all styles and return file/memory stream
     *
     * @param array        $bundles
     * @param string       $baseUrl
     * @param string       $basePath
     * @param \ArrayObject $resources
     *
     * @return string
     */
    private function buildIcons(array $bundles, $baseUrl, $basePath, \ArrayObject $resources)
    {
        $content = '';
        $content .= $this->buildIconsForDir($bundles, 'icons', $basePath, $resources);
        $content .= $this->buildIconsForDir($bundles, 'flags', $basePath, $resources);

        if (!$this->debug) {
            $content = $this->compress($content);
        }

        $content = str_replace('/makeweb/', $baseUrl . '/', $content);
        $content = str_replace('/BASEURL/', $baseUrl . '/', $content);
        $content = str_replace('/BASEPATH/', $basePath . '/', $content);

        return $content;
    }

    /**
     * @param array        $bundles
     * @param string       $assetDir
     * @param string       $baseUrl
     * @param \ArrayObject $resources
     *
     * @return string
     */
    private function buildIconsForDir(array $bundles, $assetDir, $baseUrl, \ArrayObject $resources)
    {
        $data = array();

        foreach ($bundles as $id => $class) {
            $reflection = new \ReflectionClass($class);
            $path = dirname($reflection->getFileName());

            $id = str_replace('bundle', '', strtolower($id));
            $key = $assetDir === 'icons' ? str_replace('phlexible', '', $id) : $assetDir;

            $path = $path . '/Resources/public/' . $assetDir . '/';
            if (!file_exists($path)) {
                continue;
            }

            $resources[] = new FileResource($path);

            $finder = new Finder();
            foreach ($finder->in($path)->name('*') as $iconFile) {
                /* @var $iconFile SplFileInfo */

                $ext = $iconFile->getExtension();
                if ($ext != 'png' && $ext != 'gif' && $ext != 'jpg') {
                    continue;
                }

                $icon = $iconFile->getPathname();

                $size = getimagesize($icon);
                if ($size[0] != 16 || $size[1] != 16) {
                    continue;
                }

                $name = $iconFile->getFilename();

                $selector = sprintf('.%s-%s-%s-icon', substr('p', 0, 1), $key, basename($icon, '.' . $ext));

                $data[] = array(
                    'component' => $id,
                    'file'      => $icon,
                    'name'      => $name,
                    'selector'  => $selector,
                    'ext'       => $ext,
                );
            }
        }

        $urlTemplate = $baseUrl . '/bundles/%component%/%dir%/%name%';

        $content = '';
        foreach ($data as $row) {
            $url = str_replace(
                array('%component%', '%name%', '%dir%'),
                array($row['component'], $row['name'], $assetDir),
                $urlTemplate
            );

            $style = $row['selector'] . ' {background-image: url(' . $url . ') !important;}' . PHP_EOL;

            $content .= $style;
        }

        return $content;
    }

    /**
     * CSS-aware compress the input string
     *
     * @param string $style
     *
     * @return string
     */
    private function compress($style)
    {
        return $this->cssCompressor->compressString($style);
    }
}
