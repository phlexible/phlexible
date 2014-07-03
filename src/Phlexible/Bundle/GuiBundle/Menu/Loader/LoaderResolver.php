<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Menu\Loader;

/**
 * Loader resolver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LoaderResolver implements LoaderResolverInterface
{
    /**
     * @var LoaderInterface[]
     */
    private $loaders;

    /**
     * @param LoaderInterface[] $loaders
     */
    public function __construct(array $loaders = array())
    {
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
        $this->loaders[] = $loader;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($file)
    {
        foreach ($this->loaders as $loader) {
            if ($loader->supports($file)) {
                return $loader;
            }
        }

        return null;
    }
}
