<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaType\File;

use Phlexible\Component\MediaType\Compiler\CompilerInterface;
use Phlexible\Component\MediaType\File\Loader\LoaderInterface;
use Phlexible\Component\MediaType\Model\MediaTypeCollection;
use Puli\PuliFactory;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Resource\FileResource;

/**
 * Media type loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaTypeLoader
{
    /**
     * @var PuliFactory
     */
    private $puliFactory;

    /**
     * @var CompilerInterface
     */
    private $compiler;

    /**
     * @var string
     */
    private $fileDir;

    /**
     * @var array
     */
    private $loaders = [];

    /**
     * @param PuliFactory       $puliFactory
     * @param CompilerInterface $compiler
     * @param string            $fileDir
     * @param string            $cacheDir
     * @param bool              $debug
     */
    public function __construct(PuliFactory $puliFactory, CompilerInterface $compiler, $fileDir, $cacheDir, $debug)
    {
        $this->puliFactory = $puliFactory;
        $this->compiler = $compiler;
        $this->fileDir = $fileDir;
        $this->cacheDir = $cacheDir;
        $this->debug = $debug;
    }

    /**
     * @param LoaderInterface $loader
     *
     * @return $this
     */
    public function addLoader(LoaderInterface $loader)
    {
        $this->loaders[$loader->getExtension()] = $loader;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->cacheDir . '/documenttypes' . ($this->debug ? 'Debug' : '') . '.php';
    }

    /**
     * @return MediaTypeCollection
     */
    public function loadMediaTypes()
    {
        $mediaTypes = new MediaTypeCollection();
        $configCache = new ConfigCache($this->getFilename(), $this->debug);

        if (!$configCache->isFresh()) {
            $repo = $this->puliFactory->createRepository();

            $resources = [];
            $r = new \ReflectionClass($this);
            $resources[] = new FileResource($r->getFileName());
            $r = new \ReflectionClass($this->compiler);
            $resources[] = new FileResource($r->getFileName());
            foreach ($this->loaders as $extension => $loader) {
                foreach ($repo->find("/phlexible/mediatypes/*/*.$extension") as $resource) {
                    /* @var $resource \Puli\Repository\Resource\FileResource */
                    $file = $resource->getFilesystemPath();
                    $mediaTypes->add($loader->load($file));
                    $resources[basename($file)] = new FileResource($file);
                }
            }

            $resources = array_values($resources);

            $configCache->write($this->compiler->compile($mediaTypes), $resources);
        }

        include (string) $configCache;

        $classname = $this->compiler->getClassname();
        $mediaTypes = new $classname();

        return $mediaTypes;
    }
}
