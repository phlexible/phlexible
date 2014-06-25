<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\File;

use Phlexible\Bundle\MediaTemplateBundle\File\Loader\LoaderInterface;
use Phlexible\Bundle\MediaTemplateBundle\Model\TemplateCollection;

/**
 * Template loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TemplateLoader
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
     * @var array
     */
    private $loaders = array();
    /**
     * @param array  $bundles
     * @param string $fileDir
     */
    public function __construct(array $bundles, $fileDir)
    {
        $this->bundles = $bundles;
        $this->fileDir = $fileDir;
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
     * @return TemplateCollection
     */
    public function loadTemplates()
    {
        $templates = new TemplateCollection();

        $dirs = array();
        foreach ($this->bundles as $class) {
            $reflection = new \ReflectionClass($class);
            $componentDir = dirname($reflection->getFileName()) . '/Resources/mediatemplates/';
            if (file_exists($componentDir)) {
                $dirs[] = $componentDir;
            }
        }
        $dirs[] = $this->fileDir;

        foreach ($dirs as $dir) {
            foreach ($this->loaders as $extension => $loader) {
                $files = glob($dir . '*.' . $extension);

                foreach ($files as $file) {
                    $templates->add($loader->load($file));
                }
            }
        }

        return $templates;
    }
}