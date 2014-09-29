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
use Phlexible\Bundle\GuiBundle\Locator\PatternLocator;

/**
 * Content channel loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ContentchannelLoader
{
    /**
     * @var PatternLocator
     */
    private $locator;

    /**
     * @var LoaderInterface[]
     */
    private $loaders = array();

    /**
     * @param PatternLocator $locator
     */
    public function __construct(PatternLocator $locator)
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
            $files = $this->locator->locate('*.' . $extension, 'contentchannels');

            foreach ($files as $file) {
                $contentChannels->add($loader->load($file));
            }
        }

        return $contentChannels;
    }
}