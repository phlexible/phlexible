<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ContentchannelBundle\Controller;

use Phlexible\Bundle\ContentchannelBundle\Entity\Contentchannel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Data controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/contentchannels")
 * @Security("is_granted('contentchannels')")
 */
class DataController extends Controller
{
    /**
     * List content channels
     *
     * @return JsonResponse
     * @Route("/list", name="contentchannels_list")
     */
    public function listAction()
    {
        $contentchannelManager = $this->get('phlexible_contentchannel.contentchannel_manager');

        $allContentChannels = $contentchannelManager->findAll();

        $contentChannels = array();
        foreach ($allContentChannels as $contentChannel) {
            $contentChannels[] = array(
                'id'                 => $contentChannel->getId(),
                'unique_id'          => $contentChannel->getUniqueId(),
                'title'              => $contentChannel->getTitle(),
                'renderer_classname' => $contentChannel->getRendererClassname(),
                'icon'               => $contentChannel->getIcon(),
                'template_folder'    => $contentChannel->getTemplateFolder(),
            );
        }

        return new JsonResponse(array('contentchannels' => $contentChannels));
    }

    /**
     * Create new content channel
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/create", name="contentchannels_create")
     */
    public function createAction(Request $request)
    {
        $title = $request->get('title');

        $contentchannelManager = $this->get('phlexible_contentchannel.contentchannel_manager');

        $contentChannel = new Contentchannel();
        $contentChannel
            ->setTitle($title)
            ->setRendererClassname('Makeweb_Renderers_Html');

        $contentchannelManager->updateContentchannel($contentChannel);

        return new ResultResponse(true, 'Content channel created.');
    }

    /**
     * Save content channel
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/save", name="contentchannels_save")
     */
    public function saveAction(Request $request)
    {
        $contentchannelManager = $this->get('phlexible_contentchannel.contentchannel_manager');

        $contentChannel = $contentchannelManager->find($request->get('id'))
            ->setUniqueId($request->get('unique_id'))
            ->setTitle($request->get('title'))
            ->setIcon($request->get('icon'))
            ->setTemplateFolder($request->get('template_folder'))
            ->setRendererClassname($request->get('renderer_classname'));

        $contentchannelManager->updateContentchannel($contentChannel);

        return new ResultResponse(true, 'Content channel saved.');
    }
}
