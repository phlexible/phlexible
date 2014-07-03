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

        $siteManager = $this->get('mediasite.manager');

        $site = $siteManager->getByFileId($fileId);
        $file = $site->findFile($fileId);

        $debug = array(
            array('key' => 'mimeType', 'value' => $file->getMimeType()),
            array('key' => 'documentType', 'value' => strtolower($file->getAttribute('documenttype'))),
            array('key' => 'assetType', 'value' => strtolower($file->getAttribute('assettype'))),
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

        $siteManager = $this->get('mediasite.manager');
        $cacheRepository = $this->get('mediacache.repository');
        $templateRepository = $this->get('mediatemplates.repository');

        $site = $siteManager->getByFileId($fileId);
        $file = $site->findFile($fileId);
        $cacheItems = $cacheRepository->findBy(array('file_id' => $fileId));

        $cache = array();
        foreach ($cacheItems as $cacheItem) {
            $template = $templateRepository->find($cacheItem->getTemplateKey());
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
