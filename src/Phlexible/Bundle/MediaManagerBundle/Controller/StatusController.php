<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Controller;

use Phlexible\Bundle\MediaSiteBundle\Folder\SizeCalculator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Status controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/status/media")
 * @Security("is_granted('ROLE_SUPER_ADMIN')")
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
        $sites = $this->get('phlexible_media_site.site_manager')->getAll();

        $out = '<pre>';

        foreach ($sites as $siteKey => $site) {
            $sizeCalculator = new SizeCalculator();
            $calculatedSize = $sizeCalculator->calculate($site, $site->findRootFolder());

            $out .= 'Site: ' . $siteKey . PHP_EOL;
            //            $out .= '  Driver:   ' . $site->getDriver() . PHP_EOL;
            $out .= '  ID:       ' . $site->getId() . PHP_EOL;
            $out .= '  Quota:    ' . $site->getQuota() . PHP_EOL;
            $out .= '  Size:     ' . $calculatedSize->getSize() . PHP_EOL;
            $out .= '  Folders:  ' . $calculatedSize->getNumFolders() . PHP_EOL;
            $out .= '  Files:    ' . $calculatedSize->getNumFiles() . PHP_EOL;
            $out .= '  RootDir:  ' . $site->getRootDir() . PHP_EOL;
            $out .= '    exists:   ' . (file_exists($site->getRootDir()) ? 'OK' : 'Not OK') . PHP_EOL;
            $out .= '    readable: ' . (is_readable($site->getRootDir()) ? 'OK' : 'Not OK') . PHP_EOL;
            $out .= '    writable: ' . (is_writable($site->getRootDir()) ? 'OK' : 'Not OK') . PHP_EOL;
            $out .= PHP_EOL;
        }

        return new Response($out);
    }
}
