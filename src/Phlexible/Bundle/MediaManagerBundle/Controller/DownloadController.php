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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Download controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/mediamanager/download")
 * @Security("is_granted('media')")
 */
class DownloadController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     * @Route("", name="mediamanager_download")
     */
    public function fileAction(Request $request)
    {
        $fileId = $request->get('id');
        $fileVersion = $request->get('version', null);

        $siteManager = $this->get('phlexible_media_site.site_manager');
        $site = $siteManager->getByFileId($fileId);

        if ($fileVersion) {
            $file = $site->findFile($fileId, $fileVersion);
        } else {
            $file = $site->findFile($fileId);
        }

        $filepath = $file->getPhysicalPath();
        $filename = $file->getName();

        return $this->get('igorw_file_serve.response_factory')
            ->create($filepath, $file->getMimeType(), array(
                'serve_filename' => $filename,
                'absolute_path'  => true,
                'inline'         => false,
            ));
    }

    /**
     * Stream file
     *
     * @param Request $request
     *
     * @return Response
     */
    public function zipAction(Request $request)
    {
        $filename = basename($request->get('filename'));
        $filepath = $this->container->getParameter(':media.manager.temp_dir') . $filename;

        if (!$filename || !file_exists($filepath)) {
            return $this->createNotFoundException();
        }

        return $this->get('igorw_file_serve.response_factory')
            ->create($filepath, 'application/zip', array('absolute_path' => true));
    }
}
