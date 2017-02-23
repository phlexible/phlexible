<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaTemplateBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Component\MediaTemplate\Exception\InvalidArgumentException;
use Phlexible\Component\MediaTemplate\Model\AudioTemplate;
use Phlexible\Component\MediaTemplate\Model\ImageTemplate;
use Phlexible\Component\MediaTemplate\Model\VideoTemplate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Templates controller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/mediatemplates/templates")
 * @Security("is_granted('ROLE_MEDIA_TEMPLATES')")
 */
class TemplatesController extends Controller
{
    /**
     * List mediatemplates.
     *
     * @return JsonResponse
     * @Route("/list", name="mediatemplates_templates_list")
     */
    public function listAction()
    {
        $repository = $this->get('phlexible_media_template.template_manager');

        $allTemplates = $repository->findAll();

        $templates = [];
        foreach ($allTemplates as $template) {
            if (substr($template->getKey(), 0, 4) === '_mm_') {
                continue;
            }

            $templates[] = [
                'key' => $template->getKey(),
                'type' => $template->getType(),
            ];
        }

        return new JsonResponse(['templates' => $templates]);
    }

    /**
     * Create mediatemplate.
     *
     * @param Request $request
     *
     * @throws \InvalidArgumentException
     *
     * @return ResultResponse
     * @Route("/create", name="mediatemplates_templates_create")
     */
    public function createAction(Request $request)
    {
        $templateRepository = $this->get('phlexible_media_template.template_manager');

        $type = $request->get('type');
        $key = $request->get('key');

        switch ($type) {
            case 'image':
                $template = new ImageTemplate();
                $template->setCache(false);
                break;
            case 'video':
                $template = new VideoTemplate();
                $template->setCache(true);
                break;
            case 'audio':
                $template = new AudioTemplate();
                $template->setCache(true);
                break;
            default:
                throw new InvalidArgumentException("Unknown template type $type");
        }

        $template->setKey($key);

        $templateRepository->updateTemplate($template);

        return new ResultResponse(true, 'New "'.$type.'" template "'.$key.'" created.');
    }
}
