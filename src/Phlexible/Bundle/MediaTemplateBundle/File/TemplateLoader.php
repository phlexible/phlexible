<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\File;

use Phlexible\Bundle\GuiBundle\Locator\PatternLocator;
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
     * @return TemplateCollection
     */
    public function loadTemplates()
    {
        $templates = new TemplateCollection();

        foreach ($this->loaders as $extension => $loader) {
            $files = $this->locator->locate('*.' . $extension, 'mediatemplates');

            foreach ($files as $file) {
                $templates->add($loader->load($file));
            }
        }

        return $templates;
    }
}