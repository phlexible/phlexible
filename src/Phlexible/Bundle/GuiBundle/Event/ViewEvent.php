<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Event;

use Phlexible\Bundle\GuiBundle\View\AbstractView;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * View frame event
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
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @param Request                  $request
     * @param AbstractView             $view
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(
        Request $request,
        AbstractView $view,
        SecurityContextInterface $securityContext)
    {
        $this->request = $request;
        $this->view = $view;
        $this->securityContext = $securityContext;
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

    /**
     * @return SecurityContextInterface
     */
    public function getSecurityContext()
    {
        return $this->securityContext;
    }
}