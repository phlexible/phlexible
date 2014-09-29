<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Locator;

use Symfony\Component\Config\FileLocator as BaseFileLocator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * PatternLocator uses the KernelInterface to locate resources in bundles.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PatternLocator
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * Constructor.
     *
     * @param KernelInterface $kernel A KernelInterface instance
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @param string $pattern
     * @param string $resourceDir
     *
     * @return array
     */
    public function locate($pattern, $resourceDir)
    {
        $paths = array();
        foreach ($this->kernel->getBundles() as $bundle) {
            $path = $bundle->getPath() . '/Resources/' . $resourceDir;
            if (file_exists($path)) {
                $paths[] = $path;
            }
        }
        $paths[] = $this->kernel->getRootDir() . '/Resources/' . $resourceDir;

        $finder = new Finder();
        $files = array();
        foreach ($finder->in($paths)->files()->name($pattern) as $file) {
            /* @var $file SplFileInfo */
            $files[$file->getFilename()] = $file->getPathname();
        }

        return $files;
    }
}
