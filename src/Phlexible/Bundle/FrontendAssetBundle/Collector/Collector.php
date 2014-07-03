<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendAssetBundle\Collector;

use Phlexible\Bundle\FrontendAssetBundle\Event\CollectEvent;
use Phlexible\Bundle\FrontendAssetBundle\FrontendAssetEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Collector
 *
 * @author Matthias Harmuth <mharmuth@brainbits.net>
 */
class Collector implements CollectorInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var BlockCollection
     */
    private $collection;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function collect()
    {
        if (null === $this->collection) {
            $this->collection = new BlockCollection();

            $event = new CollectEvent($this->collection);
            $this->dispatcher->dispatch(FrontendAssetEvents::COLLECT, $event);
        }

        return $this->collection;
    }
}
