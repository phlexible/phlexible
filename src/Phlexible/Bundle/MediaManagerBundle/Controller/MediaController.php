<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Controller;

use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Component\MediaTemplate\Model\ImageTemplate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Media controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/mediamanager/media")
 * @Security("is_granted('ROLE_MEDIA')")
 */
class MediaController extends Controller
{
    /**
     * Deliver a media asset
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

        try {
            $cacheItem = $cacheManager->findByTemplateAndFile($templateKey, $fileId, $fileVersion);
        } catch (\Exception $e) {
            $cacheItem = null;
        }

        $template = $templateManager->find($templateKey);

        if ($cacheItem) {
            if ($cacheItem->getCacheStatus() === CacheItem::STATUS_WAITING) {
                $queueProcessor = $this->get('phlexible_media_cache.queue_processor');
                $queueProcessor->processItem($cacheItem);
            } elseif ($cacheItem->getCacheStatus() === CacheItem::STATUS_MISSING) {
                $file = $volumeManager->getByFileId($fileId)->findFile($fileId);
                if (file_exists($file->getPhysicalPath())) {
                    $queueProcessor = $this->get('phlexible_media_cache.queue_processor');
                    $queueProcessor->processItem($cacheItem);
                }
            }

            if ($cacheItem->getCacheStatus() === CacheItem::STATUS_OK) {
                $storageKey = $template->getStorage();
                $storage = $storageManager->get($storageKey);
                $filePath = $storage->getLocalPath($cacheItem);

                if (!file_exists($filePath)) {
                    $filePath = null;

                    $queueProcessor = $this->get('phlexible_media_cache.queue_processor');
                    $queueProcessor->processItem($cacheItem);

                    if ($cacheItem->getCacheStatus() === CacheItem::STATUS_OK) {
                        $storageKey = $template->getStorage();
                        $storage = $storageManager->get($storageKey);
                        $filePath = $storage->getLocalPath($cacheItem);
                    }
                }
            }

            $mimeType = $cacheItem->getMimeType();
        } else {
            $batchBuilder = $this->get('phlexible_media_cache.batch_builder');
            $batchResolver = $this->get('phlexible_media_cache.batch_resolver');
            $queueProcessor = $this->get('phlexible_media_cache.queue_processor');

            $file = $volumeManager->getByFileId($fileId)->findFile($fileId);
            $batch = $batchBuilder->createForTemplateAndFile($template, $file);
            $queue = $batchResolver->resolve($batch);

            $cacheItem = $queueProcessor->processItem($queue->first());

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

            $file = $volumeManager->getByFileId($fileId)->findFile($fileId);
            $mediaType = $mediaTypeManager->find(strtolower($file->getMediaType()));
            $filePath = $delegateService->getClean($template, $mediaType, true);
            $mimeType = 'image/gif';
        }

        return $this->get('igorw_file_serve.response_factory')
            ->create($filePath, $mimeType, ['absolute_path' => true]);
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

        return $this->get('igorw_file_serve.response_factory')
            ->create($filePath, $mimeType, ['absolute_path' => true]);
    }
}
