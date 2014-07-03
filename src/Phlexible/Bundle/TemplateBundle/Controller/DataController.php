<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TemplateBundle\Controller;

use Phlexible\Bundle\TemplateBundle\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Data controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/templates")
 * @Security("is_granted('templates')")
 */
class DataController extends Controller
{
    /**
     * List templates
     *
     * @return JsonResponse
     * @Route("/list", name="templates_list")
     */
    public function listAction()
    {
        $templateRepository = $this->get('templates.repository');
        $allTemplates = $templateRepository->getAll();

        $templates = array();

        foreach ($allTemplates as $template) {
            /* @var $template Template */
            $templates[] = array(
                'id'       => $template->getId(),
                'name'     => $template->getName(),
                'path'     => $template->getPath(),
                'filename' => $template->getFilename(),
            );
        }

        return new JsonResponse(
            array(
                'templates' => $templates,
                'total'     => count($templates)
            )
        );
    }

    /**
     * Get Template information
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/template", name="templates_template")
     */
    public function templateAction(Request $request)
    {
        $templateId = $request->get('id');

        $templateRepository = $this->get('templates.repository');
        $template = $templateRepository->find($templateId);

        $settings = array(
            'id'       => $template->getId(),
            'name'     => $template->getName(),
            'path'     => $template->getPath(),
            'filename' => $template->getFilename(),
            'absolute' => $template->getAbsoluteFilename(),
        );

        $data = array(
            'id'       => $templateId,
            'settings' => $settings,
            'content'  => $template->getContent(),
        );

        return new JsonResponse($data);
    }
}
