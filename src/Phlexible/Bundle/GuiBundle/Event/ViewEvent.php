<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Event;

use Phlexible\Bundle\GuiBundle\View\AbstractView;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

/**
 * View frame event.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ViewEvent extends Event
{
    /**
     * @var AbstractView
     */
    private $view;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param Request      $request
     * @param AbstractView $view
     */
    public function __construct(Request $request, AbstractView $view)
    {
        $this->request = $request;
        $this->view = $view;
    }

    /**
     * @return AbstractView
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
