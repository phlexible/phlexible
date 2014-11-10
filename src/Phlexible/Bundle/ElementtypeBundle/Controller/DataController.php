<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Data controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/elementtypes/data")
 * @Security("is_granted('ROLE_ELEMENTTYPES')")
 */
class DataController extends Controller
{
    /**
     * Return available content channels
     *
     * @return JsonResponse
     * @Route("/contentchannels", name="elementtypes_data_contentchannels")
     */
    public function contentchannelsAction()
    {
        $allContentChannels = $this->get('phlexible_contentchannel.contentchannel_manager')->findAll();

        $contentChannels = array();
        foreach ($allContentChannels as $contentChannelID => $contentChannel) {
            $contentChannels[] = array(
                'id'        => $contentChannelID,
                'title'     => $contentChannel->getTitle(),
                'available' => false
            );
        }

        return new JsonResponse(array('contentChannels' => $contentChannels));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/images", name="elementtypes_data_images")
     */
    public function imagesAction(Request $request)
    {
        $locator = $this->get('pattern_locator');
        $files = $locator->locate('*.gif', 'public/elementtypes', false);
        $prefix = $request->getBasePath() . '/bundles/phlexibleelementtype/elementtypes/';

        foreach ($files as $file) {
            $data[basename($file)] = array(
                'title' => basename($file),
                'url'   => $prefix . basename($file)
            );
        }

        ksort($data);
        $data = array_values($data);

        return new JsonResponse(array('images' => $data));
    }
}
