<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Phlexible\Bundle\SiterootBundle\SiterootsMessage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Siteroot controller
 *
 * @author Phillip Look <plook@brainbits.net>
 * @Route("/siteroots/siteroot")
 * @Security("is_granted('ROLE_SITEROOTS')")
 */
class SiterootController extends Controller
{
    /**
     * List siteroots
     *
     * @return JsonResponse
     * @Route("/list", name="siteroots_siteroot_list")
     */
    public function listAction()
    {
        $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');

        $siteroots = array();
        foreach ($siterootManager->findAll() as $siteroot) {
            $siteroots[] = array(
                'id'    => $siteroot->getId(),
                'title' => $siteroot->getTitle(),
            );
        }

        return new JsonResponse(array(
            'siteroots' => $siteroots,
            'count'     => count($siteroots)
        ));
    }

    /**
     * Create siteroot
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/create", name="siteroots_siteroot_create")
     */
    public function createAction(Request $request)
    {
        $title = $request->get('title', null);

        $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');

        $siteroot = new Siteroot();
        foreach (explode(',', $this->container->getParameter('phlexible_gui.languages.available')) as $language) {
            $siteroot->setTitle($language, $title);
        }
        $siteroot
            ->setCreateUserId($this->getUser()->getId())
            ->setCreatedAt(new \DateTime())
            ->setModifyUserId($siteroot->getCreateUserId())
            ->setModifiedAt($siteroot->getCreatedAt());

        $siterootManager->updateSiteroot($siteroot);

        return new ResultResponse(true, 'New Siteroot created.');
    }

    /**
     * Delete siteroot
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/delete", name="siteroots_siteroot_delete")
     */
    public function deleteAction(Request $request)
    {
        $siterootId = $request->get('id');

        $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');

        $siteroot = $siterootManager->find($siterootId);
        $siterootManager->deleteSiteroot($siteroot);

        return new ResultResponse(true, 'Siteroot deleted.');
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("", name="siteroots_siteroot_load")
     */
    public function loadAction(Request $request)
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

        foreach ($siteroot->getSpecialTids() as $specialTid) {
            $data['specialtids'][] = array(
                'siteroot_id' => $siterootId,
                'key'         => $specialTid['name'],
                'language'    => !empty($specialTid['language']) ? $specialTid['language'] : null,
                'tid'         => $specialTid['treeId'],
            );
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
     * Save siteroot
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/save", name="siteroots_siteroot_save")
     */
    public function saveAction(Request $request)
    {
        $siterootSaver = $this->get('phlexible_siteroot.siteroot_saver');

        $siterootSaver->saveAction($request);

        return new ResultResponse(true, 'Siteroot data saved');
    }
}
