<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Status controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/status/media")
 * @Security("is_granted('debug')")
 */
class StatusController extends Controller
{
    /**
     * Show site status
     *
     * @return Response
     * @Route("", name="mediamanager_status")
     */
    public function indexAction()
    {
        $sites = $this->get('mediasite.manager')->getAll();

        $out = '<pre>';

        foreach ($sites as $siteKey => $site) {
            $out .= 'Site: ' . $siteKey . PHP_EOL;
            //            $out .= '  Driver:   ' . $site->getDriver() . PHP_EOL;
            $out .= '  ID:       ' . $site->getId() . PHP_EOL;
            $out .= '  Quota:    ' . $site->getQuota() . PHP_EOL;
            $out .= '  RootDir:  ' . $site->getRootDir() . PHP_EOL;
            $out .= '    exists:   ' . (file_exists($site->getRootDir()) ? 'OK' : 'Not OK') . PHP_EOL;
            $out .= '    readable: ' . (is_readable($site->getRootDir()) ? 'OK' : 'Not OK') . PHP_EOL;
            $out .= '    writable: ' . (is_writable($site->getRootDir()) ? 'OK' : 'Not OK') . PHP_EOL;
            $out .= PHP_EOL;
        }

        return new Response($out);
    }
}
