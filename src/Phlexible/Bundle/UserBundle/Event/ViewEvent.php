<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Event;

use Phlexible\Bundle\GuiBundle\View\AbstractView;
use Symfony\Component\EventDispatcher\Event;

/**
 * View event
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
        $this->view    = $view;
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