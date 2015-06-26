<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendMediaBundle\Controller;

use Phlexible\Component\MediaTemplate\Model\ImageTemplate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     * @Route("/thumbnail/{fileId}/{template}", name="frontendmedia_thumbnail")
     */
    public function thumbnailAction($fileId, $template)
    {
        $templateKey = str_replace('.jpg', '', $template);

        $volumeManager = $this->get('phlexible_media_manager.volume_manager');
        $templateManager = $this->get('phlexible_media_template.template_manager');

        $volume = $volumeManager->getByFileId($fileId);
        $file = $volume->findFile($fileId);
        $template = $templateManager->find($templateKey);

        $filePath = $this->container->getParameter('app.web_dir') . '/media/thumbnail/' . $fileId . '/' . $templateKey . '_' . $template->getRevision() . '.jpg';
        $mimeType = 'image/jpeg';
        if (!file_exists($filePath)) {
            if (file_exists($file->getPhysicalPath())) {
                if (!file_exists(dirname($filePath))) {
                    mkdir(dirname($filePath), 0777, true);
                }
                $this->get('phlexible_media_template.applier.image')
                    ->apply($template, $file, $file->getPhysicalPath(), $filePath);
            } else {
                if (!$template instanceof ImageTemplate) {
                    throw new NotFoundHttpException('Not found');
                }

                $mediaTypeManager = $this->get('phlexible_media_type.media_type_manager');
                $delegateService = $this->get('phlexible_media_cache.image_delegate.service');

                $mediaType = $mediaTypeManager->find($file->getMediaType());
                $filePath = $delegateService->getClean($template, $mediaType, true);
                $mimeType = 'image/gif';
            }
        }

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        return $this->get('igorw_file_serve.response_factory')
            ->create(
                $filePath,
                $mimeType, array(
                    'serve_filename' => $file->getName() . '.' . $extension,
                    'absolute_path' => true,
                )
            );
    }

    /**
     * Download a media file
     *
     * @param string $fileId
     *
     * @return Response
     * @Route("/download/{fileId}", name="frontendmedia_download")
     */
    public function downloadAction($fileId)
    {
        $volumeManager = $this->get('phlexible_media_manager.volume_manager');

        $volume = $volumeManager->getByFileId($fileId);
        $file = $volume->findFile($fileId);

        $filePath = $file->getPhysicalPath();
        $mimeType = $file->getMimeType();

        return $this->get('igorw_file_serve.response_factory')
            ->create(
                $filePath,
                $mimeType, array(
                    'serve_filename' => $file->getName(),
                    'absolute_path' => true,
                    'inline' => false,
                )
            );
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
        $volumeManager = $this->get('phlexible_media_manager.volume_manager');

        $volume = $volumeManager->getByFileId($fileId);
        $file = $volume->findFile($fileId);

        $filePath = $file->getPhysicalPath();
        $mimeType = $file->getMimeType();

        return $this->get('igorw_file_serve.response_factory')
            ->create(
                $filePath,
                $mimeType, array(
                    'serve_filename' => $file->getName(),
                    'absolute_path' => true,
                    'inline' => false,
                )
            );
    }

    /**
     * @param string $fileId
     * @param int    $size
     *
     * @return Response
     * @Route("/icon/{fileId}/{size}", name="frontendmedia_icon")
     */
    public function iconAction($fileId, $size = 16)
    {
        $volumeManager = $this->get('phlexible_media_manager.volume_manager');
        $mediaTypeManager = $this->get('phlexible_media_type.media_type_manager');

        $volume = $volumeManager->getByFileId($fileId);
        $file = $volume->findFile($fileId);
        $mimeType = $file->getMimeType();

        $mediaType = $mediaTypeManager->findByMimetype($mimeType);
        $icon = $mediaType->getIcon($size);

        return $this->get('igorw_file_serve.response_factory')
            ->create($icon, 'image/gif', ['absolute_path' => true]);
    }
}
