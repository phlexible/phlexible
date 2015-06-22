<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Locator;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * PatternLocator uses the KernelInterface to locate resources in bundles.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PatternResourceLocator extends FileLocator
{
    private $kernel;

    /**
     * Constructor.
     *
     * @param KernelInterface $kernel A KernelInterface instance
     * @param null|string     $path   The path to the global resource directory
     * @param array           $paths  An array of paths where to look for resources
     */
    public function __construct(KernelInterface $kernel, $path = null, array $paths = [])
    {
        $this->kernel = $kernel;
        if (null !== $path && !in_array($path, $paths)) {
            $paths[] = $path;
        }

        parent::__construct($paths);
    }

    /**
     * @param string $name
     * @param string $currentPath
     * @param bool   $first
     *
     * @return array
     */
    public function locate($name, $currentPath = null, $first = true)
    {
        $paths = [];
        $path = $this->kernel->getRootDir() . '/Resources/' . $currentPath;
        if (file_exists($path)) {
            $paths[] = $path;
        }
        foreach ($this->kernel->getBundles() as $bundle) {
            $path = $bundle->getPath() . '/Resources/' . $currentPath;
            if (file_exists($path)) {
                $paths[] = $path;
            }
        }

        if (!count($paths)) {
            return array();
        }

        $finder = new Finder();
        $files = [];
        foreach ($finder->in($paths)->files()->name($name) as $file) {
            /* @var $file SplFileInfo */
            if ($first) {
                return $file->getPathname();
            }
            $files[] = $file->getPathname();
        }

        return $files;
    }
}
