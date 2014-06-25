<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SecurityBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Status controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/status/security")
 * @Security("is_granted('debug')")
 */
class StatusController extends Controller
{
    /**
     * Show security status
     *
     * @return Response
     * @Route("", name="status_security")
     */
    public function indexAction()
    {
        $out = '';
        $out .= '<a href="' . $this->generateUrl('status_security_context') . '">Context</a>';
        $out .= '<a href="' . $this->generateUrl('status_security_session') . '">Session</a>';
    }
    /**
     * Show security context
     *
     * @return Response
     * @Route("/context", name="status_security_context")
     */
    public function contextAction()
    {
        $securityContext = $this->get('security.context');

        $token = $securityContext->getToken();
        $user = $token->getUser();

        $output = '<pre>';
        $output .= 'Token class: ' . get_class($token) . PHP_EOL;
        $output .= 'User class:  ' . (is_object($user) ? get_class($user) : $user) . PHP_EOL;
        $output .= PHP_EOL;
        $output .= 'Token content:'.PHP_EOL.print_r($securityContext->getToken(), 1);

        return new Response($output);
    }

    /**
     * Show session
     *
     * @param Request $request
     *
     * @return Response
     * @Route("/session", name="status_security_session")
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
                $o = 'array ' . count($value);
            } else {
                $o = $value;
            }
            $output .= '<li>'.$key . ': ' . $o . '</li>';
        }
        $output .= '</ul>';

        return new Response($output);
    }
}