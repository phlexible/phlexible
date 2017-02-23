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

use Phlexible\Bundle\ElementBundle\ContentElement\Dumper\DumperInterface;

/**
 * Chain loader.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DumpingLoader implements LoaderInterface
{
    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var DumperInterface
     */
    private $dumper;

    /**
     * @param LoaderInterface $loader
     * @param DumperInterface $dumper
     */
    public function __construct(LoaderInterface $loader, DumperInterface $dumper)
    {
        $this->loader = $loader;
        $this->dumper = $dumper;
    }

    /**
     * {@inheritdoc}
     */
    public function load($eid, $version, $language)
    {
        $contentElement = $this->loader->load($eid, $version, $language);

        if ($contentElement) {
            $this->dumper->dump($contentElement);
        }

        return $contentElement;
    }
}
