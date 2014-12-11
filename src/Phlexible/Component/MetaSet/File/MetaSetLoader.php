<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MetaSet\File;

use Phlexible\Bundle\GuiBundle\Locator\PatternResourceLocator;
use Phlexible\Component\MetaSet\File\Loader\LoaderInterface;
use Phlexible\Component\MetaSet\Model\MetaSetCollection;

/**
 * Meta set loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaSetLoader
{
    /**
     * @var PatternResourceLocator
     */
    private $locator;

    /**
     * @var LoaderInterface[]
     */
    private $loaders = [];

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
     * @return MetaSetCollection
     */
    public function loadMetaSets()
    {
        $metaSets = new MetaSetCollection();

        foreach ($this->loaders as $extension => $loader) {
            $files = $this->locator->locate("*.$extension", 'metasets', false);

            foreach ($files as $file) {
                $metaSets->add($loader->load($file));
            }
        }

        return $metaSets;
    }
}
