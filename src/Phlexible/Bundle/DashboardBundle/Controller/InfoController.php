<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DashboardBundle\Controller;

use Phlexible\Bundle\SecurityBundle\Acl\Acl;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Info controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/dashboard/info")
 */
class InfoController extends Controller
{
    /**
     * Return info
     *
     * @return Response
     * @Route("", name="dashboard_info")
     */
    public function infoAction()
    {
        $securityContext = $this->get('security.context');

        $lines = array();

        if ($securityContext->isGranted(Acl::RESOURCE_DEBUG)) {
            $lines[] = array('Release:', $this->container->getParameter('app.app_title') . ' ' . $this->container->getParameter('app.app_version'));
            $lines[] = array('Project:', $this->container->getParameter('app.project_title') . ' ' . $this->container->getParameter('app.project_version'));
            $lines[] = array('Env:', $this->container->getParameter('kernel.environment') . ($this->container->getParameter('kernel.debug') ? ' [DEBUG]' : ''));
            $lines[] = array('Host:', $_SERVER['SERVER_NAME'] . ' [' . PHP_SAPI . ']');

            $connection = $this->getDoctrine()->getConnection();
            /* @var $connection \Doctrine\DBAL\Connection */

            $lines[] = array('Default Database:', $connection->getHost() . ' / ' . $connection->getDatabase() . ' [' . $connection->getDriver()->getName() . ']');

            $lines[] = array('Session:', session_id() . ' ['.$_SERVER['REMOTE_ADDR'].']');

            $lines[] = array('User:', $this->getUser()->getUsername() . ' ['.implode(', ', $this->getUser()->getRoles()).']');

            $lines[] = array('UserAgent:', $_SERVER['HTTP_USER_AGENT']);
        } elseif ($securityContext->isGranted(Acl::RESOURCE_ADMIN)) {
            $lines[] = array('Release:', $this->container->getParameter('app.app_title') . ' ' . $this->container->getParameter('app.app_version'));
            $lines[] = array('Project:', $this->container->getParameter('app.project_title') . ' ' . $this->container->getParameter('app.project_version'));
            $lines[] = array('Env:', $this->container->getParameter('kernel.environment') . ($this->container->getParameter('kernel.debug') ? ' [DEBUG]' : ''));

            $lines[] = array('User:', $this->getUser()->getUsername() . ' ['.implode(', ', $this->getUser()->getRoles()).']');
        } else {
            $lines[] = array('Release:', $this->container->getParameter('app.app_title') . ' ' . $this->container->getParameter('app.app_version'));
            $lines[] = array('Project:', $this->container->getParameter('app.project_title') . ' ' . $this->container->getParameter('app.project_version'));
        }

        $l1 = 0;
        $l2 = 0;
        foreach ($lines as $line) {
            if (strlen($line[0]) > $l1) {
                $l1 = strlen($line[0]);
            }
            if (isset($line[1]) && strlen($line[1]) > $l2) {
                $l2 = strlen($line[1]);
            }
        }
        $table = '';
        foreach ($lines as $line) {
            $table .= str_pad($line[0], $l1 + 2);
            $table .= str_pad($line[1], $l2 + 2);
            if (isset($line[2])) {
                $table .= $line[2];
            }
            $table .= PHP_EOL;
        }
        $out = '<pre>' . $table . '</pre>';

        return new Response($out);
    }
}
