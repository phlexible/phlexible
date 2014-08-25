<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ContentElement\Loader;

use Phlexible\Bundle\ElementBundle\ContentElement\Dumper\DumperInterface;

/**
 * Chain loader
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