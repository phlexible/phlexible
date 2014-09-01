<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Usage;

use Phlexible\Bundle\ElementtypeBundle\ElementtypeEvents;
use Phlexible\Bundle\ElementtypeBundle\Entity\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\Event\ElementtypeUsageEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Usage manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UsageManager
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Elementtype $elementtype
     *
     * @return Usage[]
     */
    public function getUsage(Elementtype $elementtype)
    {
        $event = new ElementtypeUsageEvent($elementtype);
        $this->dispatcher->dispatch(ElementtypeEvents::USAGE, $event);

        return $event->getUsage();
    }
}
