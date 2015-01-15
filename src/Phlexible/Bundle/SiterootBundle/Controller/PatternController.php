<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Pattern controller
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 * @Route("/siteroots/pattern")
 * @Security("is_granted('ROLE_SITEROOTS')")
 */
class PatternController extends Controller
{
    /**
     * @return JsonResponse
     * @Route("/placeholders", name="siteroots_customtitle_placeholders")
     */
    public function placeholdersAction()
    {
        $language = 'en';

        $patternResolver = $this->get('phlexible_siteroot.pattern_resolver');

        $data = [
            'placeholders' => $patternResolver->getPlaceholders($language)
        ];

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/example", name="siteroots_customtitle_example")
     */
    public function exampleAction(Request $request)
    {
        $siterootId = $request->get('siteroot_id');
        $pattern = $request->get('pattern');
        $language = 'en';

        $siterootRepository = $this->getDoctrine()->getRepository('PhlexibleSiterootBundle:Siteroot');
        $patternResolver = $this->get('phlexible_siteroot.pattern_resolver');

        $siteroot = $siterootRepository->find($siterootId);

        return new ResultResponse(true, $patternResolver->replaceExample($siteroot, $language, $pattern));
    }
}
