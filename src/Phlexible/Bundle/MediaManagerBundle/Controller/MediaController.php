<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Controller;

use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
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
 * @Security("is_granted('media')")
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
            if ($cacheItem && $cacheItem->getTemplateKey() !== $templateKey) {
                throw new \Exception('Requested template key <=> cache item template key mismatch.');
            }
        } catch (\Exception $e) {
            $cacheItem = null;
        }

        $template = $templateManager->find($templateKey);

        if ($cacheItem) {
            if ($cacheItem && $cacheItem->getStatus() === CacheItem::STATUS_OK) {
                $storageKey = $template->getStorage();
                $storage = $storageManager->getStorage($storageKey);
                $filePath = $storage->getLocalPath($cacheItem);
            }

            if ($cacheItem && $cacheItem->getStatus() === CacheItem::STATUS_WAITING) {
                $file = $siteManager->getByFileId($fileId)->findFile($fileId);
                $documenttype = $documenttypeManager->find(strtolower($file->getAttribute('documenttype')));
                $filePath = $delegateService->getWaiting($template, $documenttype);
            }

            $fileSize = $cacheItem->getFileSize();
            $mimeType = $cacheItem->getMimeType();
        }

        if (empty($filePath) || !file_exists($filePath)) {
            $file = $siteManager->getByFileId($fileId)->findFile($fileId);
            $documenttype = $documenttypeManager->find(strtolower($file->getAttribute('documenttype')));
            $filePath = $delegateService->getClean($template, $documenttype);
            $fileSize = filesize($filePath);
            $mimeType = 'image/gif';
        }

        return $this->get('igorw_file_serve.response_factory')
            ->create($filePath, $mimeType, array('absolute_path' => true));
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
            ->create($filePath, $mimeType, array('absolute_path' => true));
    }
}