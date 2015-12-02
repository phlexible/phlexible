<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as BaseSecurityController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;

/**
 * Security controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SecurityController extends BaseSecurityController
{
    /**
     * {@inheritdoc}
     */
    protected function renderLogin(array $data)
    {
        return $this->render('PhlexibleUserBundle:Security:login.html.twig', $data);
    }

    /**
     * @return Response
     * @Route("/login_check", name="fos_user_security_check")
     */
    public function checkAction()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
    }

    /**
     * @return Response
     * @Route("/logout", name="fos_user_security_logout")
     */
    public function logoutAction()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }
}
