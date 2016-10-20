<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\View;

use Phlexible\Bundle\GuiBundle\Event\ViewEvent;
use Phlexible\Bundle\GuiBundle\GuiEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Index view
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class IndexView extends AbstractView
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
     * {@inheritdoc}
     */
    public function collect(Request $request)
    {
        $event = new ViewEvent($request, $this);
        $this->dispatcher->dispatch(GuiEvents::VIEW_FRAME, $event);

        return $this;
    }
}
