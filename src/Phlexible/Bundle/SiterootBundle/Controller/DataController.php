<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\Controller;

use Phlexible\Bundle\SiterootBundle\Entity\Url;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Content channels controller
 *
 * @author Phillip Look <plook@brainbits.net>
 * @Route("/siteroots/data")
 * @Security("is_granted('siteroots')")
 */
class DataController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("", name="siteroots_data")
     */
    public function dataAction(Request $request)
    {
        $siterootId = $request->get('id');

        $siterootRepository = $this->getDoctrine()->getRepository('PhlexibleSiterootBundle:Siteroot');
        $contentChannelRepository = $this->get('phlexible_contentchannel.contentchannel_manager');

        $siteroot = $siterootRepository->find($siterootId);

        $usedContentChannelIds = $siteroot->getContentChannelIds();
        $defaultContentChannelId = $siteroot->getDefaultContentChannelId();

        $data = array(
            'contentchannels' => array(),
            'navigations'     => array(),
            'properties'      => array(),
            'titles'          => array(),
            'shorturls'       => array(),
            'specialtids'     => array(),
            'urls'            => array(),
        );
        foreach ($contentChannelRepository->findAll() as $contentchannel) {
            $data['contentchannels'][] = array(
                'contentchannel_id' => $contentchannel->getId(),
                'contentchannel'    => $contentchannel->getTitle(),
                'used'              => in_array($contentchannel->getId(), $usedContentChannelIds),
                'default'           => $contentchannel->getId() === $defaultContentChannelId,
            );
        }

        // get all siteroot navigations
        foreach ($siteroot->getNavigations() as $navigation) {
            $data['navigations'][] = array(
                'id'         => $navigation->getId(),
                'title'      => $navigation->getTitle(),
                'handler'    => $navigation->getHandler(),
                'start_tid'  => $navigation->getStartTreeId(),
                'max_depth'  => $navigation->getMaxDepth(),
                'supports'   => '', //call_user_func(array($navigation->getHandler(), 'getSupportedFlags')),
                'flags'      => $navigation->getFlags(),
                'additional' => $navigation->getAdditional()
            );
        }

        // TODO: siteroot properties from bundles
        /*
        foreach ($componentCallback->getSiterootProperties() as $key) {
            $property = $siteroot->getProperty($key);
            $data['properties'][$key] = strlen($property) ? $property : '';
        }
        */

        foreach ($siteroot->getShortUrls() as $shortUrl) {
            $data['shorturls'][] = array(
                'id'             => $shortUrl->getId(),
                'global_default' => 0, //$shortUrl->getGlobalDefault(),
                'default'        => 0, //$shortUrl->getDefault(),
                'siteroot_id'    => $siterootId,
                'hostname'       => $shortUrl->getHostname(),
                'path'           => $shortUrl->getPath(),
                'language'       => $shortUrl->getLanguage(),
                'target'         => $shortUrl->getTarget(),
            );
        }

        foreach ($siteroot->getAllSpecialTids() as $language => $languageRow) {
            foreach ($languageRow as $key => $tid) {
                $data['specialtids'][] = array(
                    'siteroot_id' => $siterootId,
                    'key'         => $key,
                    'language'    => $language,
                    'tid'         => $tid
                );
            }
        }

        $data['titles'] = $siteroot->getTitles();

        $data['customtitles'] = array(
            'head_title'       => $siteroot->getHeadTitle(),
            'start_head_title' => $siteroot->getStartHeadTitle(),
        );

        foreach ($siteroot->getUrls() as $url) {
            $data['urls'][] = array(
                'id'             => $url->getId(),
                'global_default' => $url->isGlobalDefault(),
                'default'        => $url->isDefault(),
                'hostname'       => $url->getHostname(),
                'language'       => $url->getLanguage(),
                'target'         => $url->getTarget(),
            );
        }

        return new JsonResponse($data);
    }

    /**
     * Get the data for the siteroot grid.
     *
     * @return JsonResponse
     * @Route("/languages", name="siteroots_data_languages")
     */
    public function languagesAction()
    {
        try {
            // get all available languages
            $allLanguageKeys = $this->container->getParameter('frontend.languages.available');

            $languages = array();
            foreach ($allLanguageKeys as $languageKey) {
                /* @var $siteroot Url */
                $languages[] = array(
                    'language' => $languageKey,
                    'title'    => $languageKey,
                );
            }

            $result = array(
                'languages' => $languages,
                'count'     => count($languages)
            );
        } catch (\Exception $e) {
            $result = array();
        }

        return new JsonResponse($result);
    }

    /**
     * @return JsonResponse
     * @Route("/handlers", name="siteroots_data_handlers")
     */
    public function handlersAction()
    {
        $list = Makeweb_Navigations_Handler::getHandlers();

        $result = array();
        foreach ($list as $item) {
            $result[] = array(
                'title'    => $item,
                'supports' => call_user_func(array($item, 'getSupportedFlags')),
            );
        }

        return new JsonResponse(array('handler' => $result));
    }

}
