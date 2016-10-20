<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaType\Loader;

/**
 * LoaderResolver selects a loader for a given resource.
 *
 * A resource can be anything (e.g. a full path to a config file or a Closure).
 * Each loader determines whether it can load a resource and how.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LoaderResolver implements LoaderResolverInterface
{
    /**
     * @var LoaderInterface[] An array of LoaderInterface objects
     */
    private $loaders = array();

    /**
     * Constructor.
     *
     * @param LoaderInterface[] $loaders An array of loaders
     */
    public function __construct(array $loaders = array())
    {
        foreach ($loaders as $loader) {
            $this->addLoader($loader);
        }
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

        return false;
    }

    /**
     * Adds a loader.
     *
     * @param LoaderInterface $loader A LoaderInterface instance
     */
    public function addLoader(LoaderInterface $loader)
    {
        $this->loaders[] = $loader;
    }

    /**
     * Returns the registered loaders.
     *
     * @return LoaderInterface[] An array of LoaderInterface instances
     */
    public function getLoaders()
    {
        return $this->loaders;
    }
}
