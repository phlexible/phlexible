<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Asset\Finder;

use Puli\Discovery\Api\EditableDiscovery;
use Puli\Repository\Resource\FileResource;

/**
 * Resource finder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ResourceFinder implements ResourceFinderInterface
{
    /**
     * @var EditableDiscovery
     */
    private $puliDiscovery;

    /**
     * @param EditableDiscovery $puliDiscovery
     */
    public function __construct(EditableDiscovery $puliDiscovery)
    {
        $this->puliDiscovery = $puliDiscovery;
    }

    /**
     * {@inheritdoc}
     */
    public function findByType($typeName)
    {
        $bindings = $this->puliDiscovery->findBindings($typeName);

        $resources = array();
        foreach ($bindings as $binding) {
            foreach ($binding->getResources() as $resource) {
                /* @var $resource FileResource */
                $resources[$resource->getPath()] = $resource;
            }
        }

        return $resources;
    }
}
