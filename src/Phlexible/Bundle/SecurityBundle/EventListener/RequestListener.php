<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SecurityBundle\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Request listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RequestListener
{
    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param SecurityContextInterface $securityContext
     * @param RouterInterface          $router
     */
    public function __construct(SecurityContextInterface $securityContext, RouterInterface $router)
    {
        $this->securityContext = $securityContext;
        $this->router = $router;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onRequest(GetResponseEvent $event)
    {
        $token = $this->securityContext->getToken();

        if (!$token || !$token->isAuthenticated()) {
            return;
        }

        $user = $token->getUser();

        if (!$user || !$user instanceof UserInterface || !$user->getProperty('forcePasswordChange')) {
            return;
        }

        $allowedRoutes = array(
            'security_forcepasswordchange_view',
            'security_forcepasswordchange_check',
            'security_asset_scripts',
            'security_asset_css',
            'security_asset_icons',
            'security_asset_translations',
        );

        $request = $event->getRequest();
        if (in_array($request->attributes->get('_route'), $allowedRoutes)) {
            return;
        }

        $url = $this->router->generate('security_forcepasswordchange_view');
        $event->setResponse(new RedirectResponse($url));
    }
}
