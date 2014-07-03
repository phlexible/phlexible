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
 * @Security("is_granted('siteroots')")
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
        $siterootRepository = $this->getDoctrine()->getRepository('PhlexibleSiterootBundle:Siteroot');

        $siteroots = array();
        foreach ($siterootRepository->findAll() as $siteroot) {
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
     * @return JsonResponse
     * @Route("/create", name="siteroots_siteroot_create")
     */
    public function createAction(Request $request)
    {
        $title = $request->get('title', null);

        $em = $this->getDoctrine()->getManager();
        $siterootRepository = $this->getDoctrine()->getRepository('PhlexibleSiterootBundle:Siteroot');

        $siteroot = new Siteroot();
        foreach ($this->container->getParameter('phlexible_gui.languages.available') as $language) {
            $siteroot->setTitle($language, $title);
        }
        $siteroot->setCreateUserId($title, $this->getUser()->getId());
        $siteroot->setCreatedAt(new \DateTime());

        $em->persist($siteroot);
        $em->flush();

        $message = SiterootsMessage::create('New Siteroot created.', '', null, null, 'siteroot');
        $this->get('phlexible_message.message_poster')->post($message);

        return new JsonResponse(true, 'New Siteroot created.');
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

        $em = $this->getDoctrine()->getManager();
        $siterootRepository = $this->getDoctrine()->getRepository('PhlexibleSiterootBundle:Siteroot');

        $siteroot = $siterootRepository->find($siterootId);

        $em->remove($siteroot);
        $em->flush();

        return new ResultResponse(true, 'Siteroot deleted.');
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
        $siterootId = $request->get('id');
        $data = json_decode($request->get('data'), true);

        $em = $this->getDoctrine()->getManager();
        $siterootRepository = $this->getDoctrine()->getRepository('PhlexibleSiterootBundle:Siteroot');

        $siteroot = $siterootRepository->find($siterootId);

        $this->applyContentchannels($siteroot, $data);
        $this->applyCustomTitles($siteroot, $data);
        $this->applyNavigations($siteroot, $data);
        $this->applyProperties($siteroot, $data);
        $this->applyShortUrls($siteroot, $data);
        $this->applyUrls($siteroot, $data);

        $em->flush();

        return new ResultResponse(true, 'Siteroot data saved');
    }

    /**
     * Apply content channels
     *
     * @param Siteroot $siteroot
     * @param array    $data
     */
    private function applyContentchannels(Siteroot $siteroot, array $data)
    {
        if (!array_key_exists('contentchannels', $data)) {
            // noting to save
            return;
        }

        $contentchannelsData = $data['contentchannels'];

        $contentchannels = array();
        foreach ($contentchannelsData as $row) {
            if (!$row['used']) {
                continue;
            }

            $contentchannels[$row['contentchannel_id']] = $row['default'] ? true : false;
        }

        $siteroot->setContentChannels($contentchannels);
    }

    /**
     * Apply custom titles
     *
     * @param Siteroot $siteroot
     * @param array    $data
     */
    private function applyCustomTitles(Siteroot $siteroot, array $data)
    {
        if (!array_key_exists('customtitles', $data)) {
            // noting to save
            return;
        }

        $customTitlesData = $data['customtitles'];

        $siteroot->setHeadTitle($customTitlesData['head_title']);
        $siteroot->setStartHeadTitle($customTitlesData['start_head_title']);
    }

    /**
     * Apply custom titles
     *
     * @param Siteroot $siteroot
     * @param array    $data
     */
    private function applyProperties(Siteroot $siteroot, array $data)
    {
        if (!array_key_exists('properties', $data)) {
            // noting to save
            return;
        }

        $propertiesData = $data['properties'];

        $siteroot->setProperties($propertiesData);
    }
}
