<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Icon controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/elements/icon")
 */
class IconController extends Controller
{
    /**
     * Delivers an icon
     *
     * @param Request $request
     * @param string  $icon
     *
     * @return Response
     * @Route("/{icon}", name="elements_icon")
     */
    public function iconAction(Request $request, $icon)
    {
        $params = $request->query->all();

        $iconBuilder = $this->get('phlexible_element.icon_builder');
        $cacheFilename = $iconBuilder->getAssetPath($icon, $params);

        return new BinaryFileResponse($cacheFilename, 200, array('Content-Type' => 'image/png'), true, ResponseHeaderBag::DISPOSITION_INLINE);
    }

}
