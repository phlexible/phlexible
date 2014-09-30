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
use Phlexible\Bundle\GuiBundle\Locator\PatternResourceLocator;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Resource\FileResource;

/**
 * Document type loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DocumenttypeLoader
{
    /**
     * @var PatternResourceLocator
     */
    private $locator;

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
     * @param PatternResourceLocator $locator
     * @param CompilerInterface      $compiler
     * @param string                 $fileDir
     * @param string                 $cacheDir
     * @param bool                   $debug
     */
    public function __construct(PatternResourceLocator $locator, CompilerInterface $compiler, $fileDir, $cacheDir, $debug)
    {
        $this->locator = $locator;
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

            $resources = array();
            foreach ($this->loaders as $extension => $loader) {
                $files = $this->locator->locate("*.$extension", 'documenttypes', false);

                foreach ($files as $file) {
                    $documentTypes->add($loader->load($file));
                    $resources[basename($file)] = new FileResource($file);
                }
            }

            $resources = array_values($resources);

            $configCache->write($this->compiler->compile($documentTypes), $resources);
        }

        include (string) $configCache;

        $classname = $this->compiler->getClassname();
        $documentTypes = new $classname();

        return $documentTypes;
    }
}