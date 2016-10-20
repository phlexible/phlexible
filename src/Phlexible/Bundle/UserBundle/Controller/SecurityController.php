<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\UserBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as BaseSecurityController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Security controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SecurityController extends BaseSecurityController
{
    /**
     * @param Request $request
     *
     * @return Response
     * @Route("/login", name="fos_user_security_login")
     */
    public function loginAction(Request $request)
    {
        return parent::loginAction($request);
    }

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
