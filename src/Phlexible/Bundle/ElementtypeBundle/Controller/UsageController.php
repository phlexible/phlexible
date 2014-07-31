<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeEvents;
use Phlexible\Bundle\ElementtypeBundle\Event\ElementtypeUsageEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Usage controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/elementtypes/usage")
 * @Security("is_granted('elementtypes')")
 */
class UsageController extends Controller
{
    /**
     * Show Usage of an Element Type
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/elementtypes/usage", name="elementtypes_usage")
     */
    public function listAction(Request $request)
    {
        $id = $request->get('id');

        $elementtype = $this->get('phlexible_elementtype.service')->findElementtype($id);

        $event = new ElementtypeUsageEvent($elementtype);
        $this->get('event_dispatcher')->dispatch(ElementtypeEvents::USAGE, $event);

        $data = $event->getUsage();

        return new JsonResponse(array('list' => $data, 'total' => count($data)));
    }
}