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

use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Component\MediaCache\Queue\Instruction;
use Phlexible\Component\MediaCache\Worker\InputDescriptor;
use Phlexible\Component\MediaTemplate\Model\ImageTemplate;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
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
        $fileVersion = $request->get('file_version');
        $templateKey = $request->get('template_key');

        $cacheManager = $this->get('phlexible_media_cache.cache_manager');
        $storageManager = $this->get('phlexible_media_cache.storage_manager');
        $templateManager = $this->get('phlexible_media_template.template_manager');
        $mediaTypeManager = $this->get('phlexible_media_type.media_type_manager');
        $delegateService = $this->get('phlexible_media_cache.image_delegate.service');
        $volumeManager = $this->get('phlexible_media_manager.volume_manager');

        try {
            $cacheItem = $cacheManager->findByTemplateAndFile($templateKey, $fileId, $fileVersion);
        } catch (\Exception $e) {
            $cacheItem = null;
        }

        $template = $templateManager->find($templateKey);

        if ($cacheItem) {
            if ($cacheItem->getCacheStatus() === CacheItem::STATUS_WAITING) {
                $cacheItem = $this->doCache($fileId, $fileVersion, $template, $cacheItem);
            } elseif ($cacheItem->getCacheStatus() === CacheItem::STATUS_MISSING) {
                $cacheItem = $this->doCache($fileId, $fileVersion, $template, $cacheItem);
            }

            if ($cacheItem && $cacheItem->getCacheStatus() === CacheItem::STATUS_OK) {
                $storageKey = $template->getStorage();
                $storage = $storageManager->get($storageKey);
                $filePath = $storage->getLocalPath($cacheItem);

                if (!file_exists($filePath)) {
                    $filePath = null;
                    $cacheItem = $this->doCache($fileId, $fileVersion, $template, $cacheItem);

                    if ($cacheItem && $cacheItem->getCacheStatus() === CacheItem::STATUS_OK) {
                        $storageKey = $template->getStorage();
                        $storage = $storageManager->get($storageKey);
                        $filePath = $storage->getLocalPath($cacheItem);
                    }
                }
            }

            if ($cacheItem) {
                $mimeType = $cacheItem->getMimeType();
            }
        } else {
            $cacheItem = $this->doCache($fileId, $fileVersion, $template);

            if ($cacheItem) {
                if ($cacheItem->getCacheStatus() === CacheItem::STATUS_OK) {
                    $storageKey = $template->getStorage();
                    $storage = $storageManager->get($storageKey);
                    $filePath = $storage->getLocalPath($cacheItem);
                }
                $mimeType = $cacheItem->getMimeType();
            }
        }

        if (empty($filePath)) {
            if (!$template instanceof ImageTemplate) {
                return new Response('Not found', 404);
            }

            $file = $volumeManager->getByFileId($fileId)->findFile($fileId);
            $mediaType = $mediaTypeManager->find(strtolower($file->getMediaType()));
            $filePath = $delegateService->getClean($template, $mediaType, true);
            $mimeType = 'image/gif';
        }

        $response = new BinaryFileResponse($filePath, 200, array('Content-Type' => $mimeType));

        return $response;
    }

    /**
     * @param string            $fileId
     * @param int               $fileVersion
     * @param TemplateInterface $template
     * @param CacheItem|null    $cacheItem
     *
     * @return null|CacheItem
     */
    private function doCache($fileId, $version, TemplateInterface $template, CacheItem $cacheItem = null)
    {
        $volumeManager = $this->get('phlexible_media_manager.volume_manager');
        $instructionCreator = $this->get('phlexible_media_cache.instruction_creator');
        $instructionProcessor = $this->get('phlexible_media_cache.instruction_processor');

        $volume = $volumeManager->getByFileId($fileId);
        $file = $volume->findFile($fileId);
        if ($version && $file->getVersion() !== $version) {
            $fileVersion = $volume->findFileVersion($fileId, $version);
            if (!$file) {
                return null;
            }
            $input = InputDescriptor::fromFileVersion($fileVersion);
        } else {
            $input = InputDescriptor::fromFile($file);
        }

        if (!file_exists($file->getPhysicalPath())) {
            return null;
        }

        if ($cacheItem) {
            $instruction = new Instruction($input, $template, $cacheItem);
        } else {
            $instruction = $instructionCreator->createInstruction($input, $template);
        }
        $instructionProcessor->processInstruction($instruction);

        return $instruction->getCacheItem();
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
