<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\FrontendMediaBundle\Controller;

use Phlexible\Component\MediaTemplate\Model\ImageTemplate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
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

        try {
            $volume = $volumeManager->getByFileId($fileId);
        } catch (\Exception $e) {
            throw $this->createNotFoundException($e->getMessage(), $e);
        }

        $file = $volume->findFile($fileId);
        $template = $templateManager->find($templateKey);

        $extension = 'jpg';
        if ($template->hasParameter('format', true)) {
            $extension = $template->getParameter('format');
        }

        $filePath = $this->container->getParameter('app.web_dir') . '/media/thumbnail/' . $fileId . '/' . $templateKey . '_' . $template->getRevision() . '.' . $extension;
        $mimeType = 'image/jpeg';
        if (!file_exists($filePath) || filemtime($filePath) < $file->getModifiedAt()->format('U')) {
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

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException("File not found.");
        }

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        $response = new BinaryFileResponse($filePath, 200, array('Content-Type' => $mimeType));
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $file->getName().'.'.$extension);

        return $response;
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

        try {
            $volume = $volumeManager->getByFileId($fileId);
        } catch (\Exception $e) {
            throw $this->createNotFoundException($e->getMessage(), $e);
        }

        $file = $volume->findFile($fileId);

        $filePath = $file->getPhysicalPath();

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException("File not found.");
        }

        $mimeType = $file->getMimeType();

        $response = new BinaryFileResponse($filePath, 200, array('Content-Type' => $mimeType));
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $file->getName());

        return $response;
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

        try {
            $volume = $volumeManager->getByFileId($fileId);
        } catch (\Exception $e) {
            throw $this->createNotFoundException($e->getMessage(), $e);
        }

        $file = $volume->findFile($fileId);

        $filePath = $file->getPhysicalPath();

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException("File not found.");
        }

        $mimeType = $file->getMimeType();

        $response = new BinaryFileResponse($filePath, 200, array('Content-Type' => $mimeType));
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $file->getName());

        return $response;
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

        try {
            $volume = $volumeManager->getByFileId($fileId);
        } catch (\Exception $e) {
            throw $this->createNotFoundException($e->getMessage(), $e);
        }

        $file = $volume->findFile($fileId);
        $mimeType = $file->getMimeType();

        $mediaType = $mediaTypeManager->findByMimetype($mimeType);
        $icon = $mediaType->getIcon($size);

        $response = new BinaryFileResponse($icon, 200, array('Content-Type' => 'image/gif'));
    }
}
