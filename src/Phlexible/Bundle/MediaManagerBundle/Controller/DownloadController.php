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
     */
    public function fileAction(Request $request)
    {
        $fileId = $request->get('id');
        $fileVersion = $request->get('version', null);

        $site = $this->get('mediasite.manager')->getByFileId($fileId);
        if ($fileVersion !== null) {
            $file = $site->getFilePeer()->getById($fileId, $fileVersion);
        } else {
            $file = $site->getFilePeer()->getById($fileId);
        }
        $filepath = $file->getFilePath();
        $filename = $file->getName();
        $filesize = $file->getSize();

        return $this->get('igorw_file_serve.response_factory')
            ->create($filepath, $file->getMimeType(), array('absolute_path' => true));
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
