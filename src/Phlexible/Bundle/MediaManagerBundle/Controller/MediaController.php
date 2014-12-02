<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Controller;

use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Bundle\MediaTemplateBundle\Model\ImageTemplate;
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
        $documenttypeManager = $this->get('phlexible_documenttype.documenttype_manager');
        $delegateService = $this->get('phlexible_media_cache.image_delegate.service');
        $siteManager = $this->get('phlexible_media_site.site_manager');

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
            }

            if ($cacheItem->getCacheStatus() === CacheItem::STATUS_OK) {
                $storageKey = $template->getStorage();
                $storage = $storageManager->get($storageKey);
                $filePath = $storage->getLocalPath($cacheItem);
            }

            $mimeType = $cacheItem->getMimeType();
        } else {
            $batchBuilder = $this->get('phlexible_media_cache.batch_builder');
            $batchResolver = $this->get('phlexible_media_cache.batch_resolver');
            $queueProcessor = $this->get('phlexible_media_cache.queue_processor');

            $file = $siteManager->getByFileId($fileId)->findFile($fileId);
            $batch = $batchBuilder->createForTemplateAndFile($template, $file);
            $queue = $batchResolver->resolve($batch);

            $cacheItem = $queue->first();
            $queueProcessor->processItem($cacheItem);

            $mimeType = $cacheItem->getMimeType();
        }

        if (empty($filePath)) {
            if (!$template instanceof ImageTemplate) {
                return new Response('Not found', 404);
            }

            $file = $siteManager->getByFileId($fileId)->findFile($fileId);
            $documenttype = $documenttypeManager->find(strtolower($file->getDocumenttype()));
            $filePath = $delegateService->getClean($template, $documenttype);
            $mimeType = 'image/gif';
        }

        return $this->get('igorw_file_serve.response_factory')
            ->create($filePath, $mimeType, ['absolute_path' => true]);
    }

    /**
     * @param string $templateKey
     * @param string $documenttypeKey
     *
     * @return Response
     * @Route("/delegate/{templateKey}/{documenttypeKey}", name="mediamanager_media_delegate")
     */
    public function delegateAction($templateKey, $documenttypeKey)
    {
        $documenttypeManager = $this->get('phlexible_documenttype.documenttype_manager');
        $templateManager = $this->get('phlexible_media_template.template_manager');
        $delegateService = $this->get('phlexible_media_cache.image_delegate.service');

        $template = $templateManager->find($templateKey);

        $documenttype = $documenttypeManager->find(strtolower($documenttypeKey));
        $filePath = $delegateService->getClean($template, $documenttype);
        $fileSize = filesize($filePath);
        $mimeType = 'image/gif';

        return $this->get('igorw_file_serve.response_factory')
            ->create($filePath, $mimeType, ['absolute_path' => true]);
    }
}
