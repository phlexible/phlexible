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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Debug controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/mediamanager/debug")
 * @Security("is_granted('debug')")
 */
class DebugController extends Controller
{
    /**
     * File debug info
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/file", name="mediamanger_debug_file")
     */
    public function fileAction(Request $request)
    {
        $fileId = $request->get('file_id');

        $siteManager = $this->get('phlexible_media_site.site_manager');

        $site = $siteManager->getByFileId($fileId);
        $file = $site->findFile($fileId);

        $debug = array(
            array('key' => 'mimeType', 'value' => $file->getMimeType()),
            array('key' => 'documentType', 'value' => strtolower($file->getDocumenttype())),
            array('key' => 'assetType', 'value' => strtolower($file->getAssettype())),
            array('key' => 'fileId', 'value' => $file->getId()),
            array('key' => 'folderId', 'value' => $file->getFolderId()),
        );

        return new JsonResponse(array('debug' => $debug));
    }

    /**
     * File debug info
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/cache", name="mediamanger_debug_cache")
     */
    public function cacheAction(Request $request)
    {
        $fileId = $request->get('file_id');

        $siteManager = $this->get('phlexible_media_site.site_manager');
        $cacheManager = $this->get('phlexible_media_cache.cache_manager');
        $templateManager = $this->get('phlexible_media_template.template_manager');

        $site = $siteManager->getByFileId($fileId);
        $file = $site->findFile($fileId);
        $cacheItems = $cacheManager->findBy(array('file_id' => $fileId));

        $cache = array();
        foreach ($cacheItems as $cacheItem) {
            $template = $templateManager->find($cacheItem->getTemplateKey());
            $storageKey = $template->getStorage();
            $storage = $this->get('mediacache.storage.' . strtolower($storageKey));

            $urls = $storage->getCacheUrls($file, $cacheItem, $this->_request->getBaseUrl());

            $cache[] = array(
                'key'   => $cacheItem->getTemplateKey(),
                'value' => $urls['media']
            );
        }

        return new JsonResponse(array('cache' => $cache));
    }
}
