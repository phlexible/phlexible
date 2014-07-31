<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ContentchannelBundle\File;

use Phlexible\Bundle\ContentchannelBundle\File\Loader\LoaderInterface;
use Phlexible\Bundle\ContentchannelBundle\Model\ContentchannelCollection;

/**
 * Content channel loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ContentchannelLoader
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
     * @return ContentchannelCollection
     */
    public function loadContentchannels()
    {
        $contentChannels = new ContentchannelCollection();

        $dirs = array();
        foreach ($this->bundles as $class) {
            $reflection = new \ReflectionClass($class);
            $componentDir = dirname($reflection->getFileName()) . '/_content_channels/';
            if (file_exists($componentDir)) {
                $dirs[] = $componentDir;
            }
        }
        $dirs[] = $this->fileDir;

        foreach ($dirs as $dir) {
            foreach ($this->loaders as $extension => $loader) {
                $files = glob($dir . '*.' . $extension);

                foreach ($files as $file) {
                    $contentChannels->add($loader->load($file));
                }
            }
        }

        return $contentChannels;
    }
}