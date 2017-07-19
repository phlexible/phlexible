<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaManagerBundle\Controller;

use Phlexible\Component\MediaCache\Domain\CacheItem;
use Phlexible\Component\MediaCache\Queue\Instruction;
use Phlexible\Component\MediaTemplate\Model\ImageTemplate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Media controller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/mediamanager/media")
 * @Security("is_granted('ROLE_MEDIA')")
 */
class MediaController extends Controller
{
    /**
     * Deliver a media asset.
     *
     * @param Request $request
     *
     * @return Response
     * @Route("", name="mediamanager_media")
     */
    public function mediaAction(Request $request)
    {
        $fileId = $request->get('file_id');
        $fileVersion = $request->get('file_version', 1);
        $templateKey = $request->get('template_key');

        $cacheManager = $this->get('phlexible_media_cache.cache_manager');
        $storageManager = $this->get('phlexible_media_cache.storage_manager');
        $templateManager = $this->get('phlexible_media_template.template_manager');
        $mediaTypeManager = $this->get('phlexible_media_type.media_type_manager');
        $delegateService = $this->get('phlexible_media_cache.image_delegate.service');
        $volumeManager = $this->get('phlexible_media_manager.volume_manager');
        $instructionCreator = $this->get('phlexible_media_cache.instruction_creator');
        $instructionProcessor = $this->get('phlexible_media_cache.instruction_processor');

        try {
            $cacheItem = $cacheManager->findByTemplateAndFile($templateKey, $fileId, $fileVersion);
        } catch (\Exception $e) {
            $cacheItem = null;
        }

        $template = $templateManager->find($templateKey);

        if ($cacheItem) {
            if ($cacheItem->getCacheStatus() === CacheItem::STATUS_WAITING) {
                $file = $volumeManager->getByFileId($fileId)->findFile($fileId);
                $instruction = new Instruction($file, $template, $cacheItem);
                $instructionProcessor->processInstruction($instruction);
            } elseif ($cacheItem->getCacheStatus() === CacheItem::STATUS_MISSING) {
                $file = $volumeManager->getByFileId($fileId)->findFile($fileId);
                if (file_exists($file->getPhysicalPath())) {
                    $instruction = new Instruction($file, $template, $cacheItem);
                    $instructionProcessor->processInstruction($instruction);
                }
            }

            if ($cacheItem->getCacheStatus() === CacheItem::STATUS_OK) {
                $storageKey = $template->getStorage();
                $storage = $storageManager->get($storageKey);
                $filePath = $storage->getLocalPath($cacheItem);

                if (!file_exists($filePath)) {
                    $file = $volumeManager->getByFileId($fileId)->findFile($fileId);
                    $filePath = null;

                    $instruction = new Instruction($file, $template, $cacheItem);
                    $instructionProcessor->processInstruction($instruction);

                    if ($cacheItem->getCacheStatus() === CacheItem::STATUS_OK) {
                        $storageKey = $template->getStorage();
                        $storage = $storageManager->get($storageKey);
                        $filePath = $storage->getLocalPath($cacheItem);
                    }
                }
            }

            $mimeType = $cacheItem->getMimeType();
        } else {
            $file = $volumeManager->getByFileId($fileId)->findFile($fileId, $fileVersion);
            $instruction = $instructionCreator->createInstruction($file, $template);
            $instructionProcessor->processInstruction($instruction);

            $cacheItem = $instruction->getCacheItem();

            if ($cacheItem->getCacheStatus() === CacheItem::STATUS_OK) {
                $storageKey = $template->getStorage();
                $storage = $storageManager->get($storageKey);
                $filePath = $storage->getLocalPath($cacheItem);
            }
            $mimeType = $cacheItem->getMimeType();
        }

        if (empty($filePath)) {
            if (!$template instanceof ImageTemplate) {
                return new Response('Not found', 404);
            }

            $file = $volumeManager->getByFileId($fileId)->findFile($fileId, $fileVersion);
            $mediaType = $mediaTypeManager->find(strtolower($file->getMediaType()));
            $filePath = $delegateService->getClean($template, $mediaType, true);
            $mimeType = 'image/gif';
        }

        $response = new BinaryFileResponse($filePath, 200, array('Content-Type' => $mimeType));

        return $response;
    }

    /**
     * @param string $templateKey
     * @param string $mediaTypeName
     *
     * @return Response
     * @Route("/delegate/{templateKey}/{mediaTypeName}", name="mediamanager_media_delegate")
     */
    public function delegateAction($templateKey, $mediaTypeName)
    {
        $mediaTypeManager = $this->get('phlexible_media_type.media_type_manager');
        $templateManager = $this->get('phlexible_media_template.template_manager');
        $delegateService = $this->get('phlexible_media_cache.image_delegate.service');

        $template = $templateManager->find($templateKey);

        $mediaType = $mediaTypeManager->find(strtolower($mediaTypeName));
        $filePath = $delegateService->getClean($template, $mediaType);
        $fileSize = filesize($filePath);
        $mimeType = 'image/gif';

        $response = new BinaryFileResponse($filePath, 200, array('Content-Type' => $mimeType));

        return $response;
    }
}
