<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendMediaBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Media controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/media")
 */
class MediaController extends Controller
{
    /**
     * Deliver a media asset
     *
     * @param string $fileId
     * @param string $template
     *
     * @return Response
     * @Route("/{fileId}/{template}", name="frontendmedia_media")
     */
    public function mediaAction($fileId, $template)
    {
        $templateKey = str_replace('.jpg', '', $template);

        $file = $this->get('phlexible_media_site.site_manager')->getByFileId($fileId)->findFile($fileId);
        $template = $this->get('phlexible_media_template.template_manager')->find($templateKey);

        $outfile = $this->container->getParameter('app.web_dir') . '/media/' . $fileId . '/' . $templateKey . '.jpg';
        if (!file_exists($outfile)) {
            if (!file_exists(dirname($outfile))) {
                mkdir(dirname($outfile), 0777, true);
            }
            $this->get('phlexible_media_template.applier.image')->apply($template, $file, $file->getPhysicalPath(), $outfile);
        }

        return $this->get('igorw_file_serve.response_factory')
            ->create($outfile, 'image/jpeg', array('absolute_path' => true));
    }

    /**
     * Deliver a flash asset
     *
     * @param string $fileId
     *
     * @return Response
     * @Route("/{fileId}", name="frontendmedia_flash")
     */
    public function flashAction($fileId)
    {
        $filePath = null;
        $mimeType = null;
        $fileName = null;

        try {
            $file = $this->getMediaItem($fileId);

            $filePath = $file->getFilePath();
            $mimeType = $file->getMimeType();
            $fileName = $file->getName();
        } catch (\Exception $e) {
            return new Response('Not found.', 404);
        }

        return $this->get('igorw_file_serve.response_factory')
            ->create($filePath, $mimeType, array('absolute_path' => true));
    }

    /**
     * Deliver a media asset
     *
     * @param string $fileId
     *
     * @return Response
     * @Route("/download/{fileId}", name="frontendmedia_download")
     */
    public function downloadAction($fileId)
    {
        $track = false;
        $filePath = null;
        $mimeType = null;
        $fileName = null;

        try {
            $cacheItem = $this->getCacheItem($fileId);

            $filePath = $cacheItem->getFilePath();
            $mimeType = $cacheItem->getMimeType();

            $file = $this->getMediaItem($cacheItem->getFileId());
            $fileName = $file->getName();

            if ($track) {
                $downloads = $this->getContainer()->get('frontendmediamanagerDownloads');
                $downloads->track($file);
            }
        } catch (\Exception $e) {
        }

        if ($mimeType === null) {
            try {
                $file = $this->getMediaItem($fileId);

                $filePath = $file->getFilePath();
                $mimeType = $file->getMimeType();
                $fileName = $file->getName();

                if ($track) {
                    $downloads = $this->getContainer()->get('frontendmediamanagerDownloads');
                    $downloads->track($file);
                }
            } catch (\Exception $e) {
                return new Response('Not found.', 404);
            }
        }

        return $this->get('igorw_file_serve.response_factory')
            ->create($filePath, $mimeType, array('absolute_path' => true, 'inline' => false));
    }

    /**
     * Deliver a media asset
     *
     * @param string $fileId
     *
     * @return Response
     * @Route("/inline/{fileId}", name="frontendmedia_inline")
     */
    public function inlineAction($fileId)
    {
        $id = $this->_getParam('id');

        $response = $this->_getResponse($id);

        $container = $this->getContainer();
        $contactManager = $container->get('contactsManager');

        $extranetMediaInstalled = $container->components->has('extranetmedia');
        if ($extranetMediaInstalled) {
            /* @var $extranetMediaViewRights Makeweb_ExtranetMedia_ViewRights */
            $extranetMediaViewRights = $container->extranetMediaViewRights;

            $authenticated = $contactManager->isAuthenticated();
            if (!$authenticated) {
                $this->_response->setHttpResponseCode(403);

                return;
            }

            $contact = Makeweb_Contacts_Contact::getInstance();
            $contact->setNew(false);

            $file = $this->getMediaItem($id);
            $folderId = $file->getFolderID();
            if ($extranetMediaViewRights->hasRightsSet($folderId)) {
                if ($extranetMediaViewRights->hasViewRight($folderId)) {
                    $contentDisposition = Brainbits_Http_Response::MODE_INLINE;
                    $response->setContentDisposition($contentDisposition)
                        ->send();
                } else {
                    $this->_response->setHttpResponseCode(403);

                    return;
                }
            }
        }

        return $this->get('igorw_file_serve.response_factory')
            ->create($filePath, $mimeType, array('absolute_path' => true));
    }

    /**
     * @param string $id
     * @param int    $size
     *
     * @return Response
     * @Route("/icon/{id}/{size}", name="frontendmedia_icon")
     */
    public function iconAction($id, $size = 16)
    {
        $mimeType = null;

        try {
            $cacheItem = $this->getCacheItem($id);
            $mimeType = $cacheItem->getMimeType();
        } catch (\Exception $e) {
        }

        if ($mimeType === null) {
            try {
                $file = $this->getMediaItem($id);
                $mimeType = $file->getMimeType();
            } catch (\Exception $e) {
                return new Response('Not found.', 404);
            }
        }

        $documentType = $this->get('documenttypes.repository')->getByMimetype($mimeType);
        $icon = $documentType->getIcon($size);

        return $this->get('igorw_file_serve.response_factory')
            ->create($icon, 'image/gif', array('absolute_path' => true));
    }

    protected function getCacheItem($id)
    {
        $cacheManager = Media_Cache_Manager::getInstance();

        return $cacheManager->getById($id);
    }

    protected function getMediaItem($id)
    {
        $site = Media_Site_Manager::getInstance()->get('mediamanager');

        return $site->getFilePeer()->getById($id);
    }
}
