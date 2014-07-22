<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SecurityBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Security controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/security")
 */
class SecurityController extends Controller
{
    /**
     * Show login page
     *
     * @param Request $request
     *
     * @return Response
     * @Route("/login", name="security_login")
     * @Template
     */
    public function loginAction(Request $request)
    {
        $loginView = $this->get('phlexible_security.view.login');

        $securityContext = $this->get('security.context');

        $csrfProvider = $this->get('form.csrf_provider');
        $csrfToken = $csrfProvider->generateCsrfToken('authenticate');

        $session = $this->get('session');
        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContextInterface::AUTHENTICATION_ERROR);
            $error = $error->getMessage();
        } elseif (null !== $session && $session->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
            $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
            $error = $error->getMessage();
        } else {
            $error = '';
        }

        if ($request->attributes->has(SecurityContextInterface::LAST_USERNAME)) {
            $lastUsername = $request->attributes->get(SecurityContextInterface::LAST_USERNAME);
        } elseif (null !== $session && $session->has(SecurityContextInterface::LAST_USERNAME)) {
            $lastUsername = $session->get(SecurityContextInterface::LAST_USERNAME);
        } else {
            $lastUsername = '';
        }

        return array(
            'baseUrl'        => $request->getBaseUrl(),
            'basePath'       => $request->getBasePath(),
            'componentsPath' => '/bundles',
            'debug'          => $this->container->getParameter('kernel.debug'),
            'theme'          => 'default',
            'language'       => 'en',
            'appTitle'       => $this->container->getParameter('app.app_title'),
            'appVersion'     => $this->container->getParameter('app.app_version'),
            'appUrl'         => $this->container->getParameter('app.app_url'),
            'projectTitle'   => $this->container->getParameter('app.project_title'),
            'scripts'        => $loginView->get($request, $securityContext),
            'csrfToken'      => $csrfToken,
            'checkPath'      => $this->generateUrl('security_check'),
            'targetUrl'      => $this->generateUrl('gui_index'),
            'resetUrl'       => $this->generateUrl('security_reset_validate_view'),
            'error'          => $error,
            'lastUsername'   => $lastUsername,
            'noScript'       => $loginView->getNoScript(
                $request->getBaseUrl(),
                $this->container->getParameter('app.app_title'),
                $this->container->getParameter('app.project_title')
            ),
        );
    }

    /**
     * Check
     *
     * @Route("/check", name="security_check")
     */
    public function checkAction()
    {
    }

    /**
     * Logout
     *
     * @Route("/logout", name="security_logout")
     */
    public function logoutAction()
    {
    }
}
