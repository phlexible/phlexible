<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendAssetBundle\Event;

use Phlexible\Bundle\FrontendAssetBundle\Collector\BlockCollection;
use Symfony\Component\EventDispatcher\Event;

/**
 * Collect event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CollectEvent extends Event
{
    /**
     * @var BlockCollection
     */
    private $collection;

    /**
     * @param BlockCollection $collection
     */
    public function __construct(BlockCollection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return BlockCollection
     */
    public function getBlockCollection()
    {
        return $this->collection;
    }
}