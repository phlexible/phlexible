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
use Phlexible\Bundle\GuiBundle\Locator\PatternResourceLocator;

/**
 * Content channel loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ContentchannelLoader
{
    /**
     * @var PatternResourceLocator
     */
    private $locator;

    /**
     * @var LoaderInterface[]
     */
    private $loaders = array();

    /**
     * @param PatternResourceLocator $locator
     */
    public function __construct(PatternResourceLocator $locator)
    {
        $this->locator = $locator;
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

        foreach ($this->loaders as $extension => $loader) {
            $files = $this->locator->locate("*.$extension", 'contentchannels');

            foreach ($files as $file) {
                $contentChannels->add($loader->load($file));
            }
        }

        return $contentChannels;
    }
}