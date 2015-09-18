<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\EventListener;

use Phlexible\Bundle\TreeBundle\Mediator\ViewableVoterInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RequestContextAwareInterface;

/**
 * Preview listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PreviewListener implements EventSubscriberInterface
{
    /**
     * @var RequestContextAwareInterface
     */
    private $router;

    /**
     * @var ViewableVoterInterface
     */
    private $viewableVoter;

    /**
     * @param RequestContextAwareInterface $router
     * @param ViewableVoterInterface       $viewableVoter
     */
    public function __construct(RequestContextAwareInterface $router, ViewableVoterInterface $viewableVoter)
    {
        $this->router = $router;
        $this->viewableVoter = $viewableVoter;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            // must be registered after the Router to have access to the _locale
            KernelEvents::REQUEST => array(array('onKernelRequest', 14)),
        );
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $this->setRouterContext($request);
    }

    /**
     * @param Request $request
     */
    private function setRouterContext(Request $request)
    {
        if ($request->attributes->get('_preview')) {
            $this->router->getContext()->setParameter('_preview', $request->attributes->get('_preview'));
            $this->viewableVoter->disablePublishCheck();
        }
    }
}
