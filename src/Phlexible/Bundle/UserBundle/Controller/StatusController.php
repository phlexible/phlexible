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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Status controller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/status/user")
 * @Security("is_granted('ROLE_SUPER_ADMIN')")
 */
class StatusController extends Controller
{
    /**
     * Show security status.
     *
     * @return Response
     * @Route("", name="phlexible_status_user")
     */
    public function indexAction()
    {
        $body = '';
        $body .= '<a href="'.$this->generateUrl('phlexible_status_user_context').'">Context</a><br />';
        $body .= '<a href="'.$this->generateUrl('phlexible_status_user_session').'">Session</a>';

        return new Response($body);
    }

    /**
     * Show security context.
     *
     * @return Response
     * @Route("/context", name="phlexible_status_user_context")
     */
    public function contextAction()
    {
        $tokenStorage = $this->get('security.token_storage');

        $token = $tokenStorage->getToken();
        $user = $token->getUser();

        $output = '<pre>';
        $output .= 'Token class: '.get_class($token).PHP_EOL;
        $output .= 'User class:  '.(is_object($user) ? get_class($user) : $user).PHP_EOL;
        $output .= PHP_EOL;
        $output .= 'Token username: ';
        $output .= print_r($token->getUsername(), 1).PHP_EOL;
        $output .= 'Token attributes: ';
        $output .= print_r($token->getAttributes(), 1).PHP_EOL;
        $output .= 'Token credentials: ';
        $output .= print_r($token->getCredentials(), 1).PHP_EOL;
        $output .= 'Token roles: ';
        $output .= print_r($token->getRoles(), 1).PHP_EOL;

        return new Response($output);
    }

    /**
     * Show session.
     *
     * @param Request $request
     *
     * @return Response
     * @Route("/session", name="phlexible_status_user_session")
     */
    public function sessionAction(Request $request)
    {
        $output = '<pre>';
        $output .= 'Security session namespace:'.PHP_EOL;
        $output .= '<ul>';
        foreach ($request->getSession()->all() as $key => $value) {
            if (is_object($value)) {
                $o = get_class($value);
            } elseif (is_array($value)) {
                $o = 'array '.count($value);
            } else {
                $o = $value;
                if (@unserialize($o)) {
                    $o = unserialize($o);
                }
            }
            $output .= '<li>'.$key.': '.$o.'</li>';
        }
        $output .= '</ul>';

        return new Response($output);
    }
}
