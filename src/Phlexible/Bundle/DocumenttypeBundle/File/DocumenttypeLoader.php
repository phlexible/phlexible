<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DocumenttypeBundle\File;

use Phlexible\Bundle\DocumenttypeBundle\Compiler\CompilerInterface;
use Phlexible\Bundle\DocumenttypeBundle\File\Loader\LoaderInterface;
use Phlexible\Bundle\DocumenttypeBundle\Model\DocumenttypeCollection;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Document type loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DocumenttypeLoader
{
    /**
     * @var array
     */
    private $bundles;

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
    private $loaders = array();

    /**
     * @param array             $bundles
     * @param CompilerInterface $compiler
     * @param string            $fileDir
     * @param string            $cacheDir
     * @param bool              $debug
     */
    public function __construct(array $bundles, CompilerInterface $compiler, $fileDir, $cacheDir, $debug)
    {
        $this->bundles = $bundles;
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
     * @return DocumenttypeCollection
     */
    public function loadDocumenttypes()
    {
        $documentTypes = new DocumenttypeCollection();
        $configCache = new ConfigCache($this->getFilename(), $this->debug);

        if (!$configCache->isFresh()) {
            $dirs = array();
            foreach ($this->bundles as $class) {
                $reflection = new \ReflectionClass($class);
                $componentDir = dirname($reflection->getFileName()) . '/Resources/documenttypes/';
                if (file_exists($componentDir)) {
                    $dirs[] = $componentDir;
                }
            }
            $dirs[] = $this->fileDir;

            $resources = array();
            foreach ($dirs as $dir) {
                foreach ($this->loaders as $extension => $loader) {
                    $files = glob($dir . '*.' . $extension);

                    foreach ($files as $file) {
                        $documentTypes->add($loader->load($file));
                        $resources[] = new FileResource($file);
                    }
                }
            }

            $configCache->write($this->compiler->compile($documentTypes), $resources);
        }

        include (string) $configCache;

        $classname = $this->compiler->getClassname();
        $documentTypes = new $classname();

        return $documentTypes;
    }
}