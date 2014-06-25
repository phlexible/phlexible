<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Form controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/mediatemplates/form")
 * @Security("is_granted('mediatemplates')")
 */
class FormController extends Controller
{
    /**
     * List variables
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/load", name="mediatemplates_form_load")
     */
    public function loadAction(Request $request)
    {
        $repository = $this->get('mediatemplates.repository');

        $templateKey = $request->get('template_key');

        $template   = $repository->find($templateKey);
        $parameters = $template->getParameters();

        if (isset($parameters['method'])) {
            $parameters['xmethod'] = $parameters['method'];
            unset($parameters['method']);
        }

        return new JsonResponse(array('success' => true, 'data' => $parameters));
    }

    /**
     * Save variables
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/save", name="mediatemplates_form_save")
     */
    public function saveAction(Request $request)
    {
        $repository = $this->get('mediatemplates.repository');

        $templateKey = $request->get('template_key');
        $params = $this->getAllParams();

        unset($params['template_key'],
              $params['module'],
              $params['controller'],
              $params['action']);

        $template = $repository->find($templateKey);

        $params = $this->fixParams($params);

        foreach ($params as $key => $value) {
            $template->setParameter($key, $value);
        }

        $repository->save($template);

        return new ResultResponse(true, 'Media template "' . $template->getKey() . '" saved.');
    }

    /**
     * @param array $params
     *
     * @return array
     */
    private function fixParams(array $params)
    {
        $qualityOverride = false;

        foreach ($params as $key => $value) {
            if ($key == 'xmethod') {
                $params['method'] = $value;
                unset($params['xmethod']);
            } elseif ($key == 'backgroundcolor' && !preg_match('/^\#[0-9A-Za-z]{6}$/', $value)) {
                $params['backgroundcolor'] = '';
            } elseif ($key == 'compression') {
                if (!$qualityOverride) {
                    $params['quality'] = 0;
                }
                $params['quality'] = $params['quality'] + $value * 10;
                $qualityOverride = true;
                unset($params['compression']);
            } elseif ($key == 'filtertype') {
                if (!$qualityOverride) {
                    $params['quality'] = 0;
                }
                $params['quality'] = $params['quality'] + $value;
                $qualityOverride = true;
                unset($params['filtertype']);
            }
        }

        return $params;
    }
}