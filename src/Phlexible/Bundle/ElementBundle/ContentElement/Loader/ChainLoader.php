<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ContentElement\Loader;

use Phlexible\Bundle\ElementBundle\ContentElement\ContentElement;
use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersionMappedField;

/**
 * Chain loader
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
