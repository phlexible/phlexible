<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\ContentElement\Loader;

/**
 * Chain loader.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ChainLoader implements LoaderInterface
{
    /**
     * @var array LoaderInterface
     */
    private $loaders = array();

    /**
     * @param LoaderInterface[] $loaders
     */
    public function __construct(array $loaders = array())
    {
        $this->loaders = $loaders;
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
    public function load($eid, $version, $language)
    {
        foreach ($this->loaders as $loader) {
            $contentElement = $loader->load($eid, $version, $language);
            if ($contentElement) {
                return $contentElement;
            }
        }

        return null;
    }
}
