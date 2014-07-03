<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\MetaSet;

use Phlexible\Bundle\MetaSetBundle\Loader\LoaderInterface;

/**
 * Meta set loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaSetLoader
{
    /**
     * @var array
     */
    private $bundles;

    /**
     * @var string
     */
    private $fileDir;

    /**
     * @var LoaderInterface[]
     */
    private $loaders = array();

    /**
     * @param array             $bundles
     * @param string            $fileDir
     * @param LoaderInterface[] $loaders
     */
    public function __construct(array $bundles, $fileDir, array $loaders = array())
    {
        $this->bundles = $bundles;
        $this->fileDir = $fileDir;

        foreach ($loaders as $loader) {
            $this->addLoader($loader);
        }
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
     * @return MetaSetCollection
     */
    public function loadMetaSets()
    {
        $metaSets = new MetaSetCollection();

        $dirs = array();
        foreach ($this->bundles as $class) {
            $reflection = new \ReflectionClass($class);
            $componentDir = dirname($reflection->getFileName()) . '/Resources/metasets/';
            if (file_exists($componentDir)) {
                $dirs[] = $componentDir;
            }
        }
        $dirs[] = $this->fileDir;

        foreach ($dirs as $dir) {
            foreach ($this->loaders as $extension => $loader) {
                $files = glob($dir . '*.' . $extension);

                foreach ($files as $file) {
                    $metaSets->add($loader->load($file));
                }
            }
        }

        return $metaSets;
    }
}