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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Preview controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/mediatemplates/preview")
 * @Security("is_granted('ROLE_MEDIA_TEMPLATES')")
 */
class PreviewController extends Controller
{
    /**
     * List Image Mediatemplates
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/image", name="mediatemplates_preview_image")
     */
    public function imageAction(Request $request)
    {
        $params = $request->request->all();

        unset($params['module']);
        unset($params['controller']);
        unset($params['action']);

        if (empty($params['width'])) {
            $params['width']  = 100;
        } if (empty($params['height'])) {
            $params['height'] = 100;
        } if (empty($params['xmethod'])) {
            $params['xmethod'] = 'fit';
        }

        $previewer = $this->get('phlexible_media_template.preview.image');
        $data = $previewer->create($params);

        return new ResultResponse(true, 'Preview created', $data);
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/audio", name="mediatemplates_preview_audio")
     */
    public function audioAction(Request $request)
    {
        $params = $request->request->all();

        unset($params['module']);
        unset($params['controller']);
        unset($params['action']);

        $previewer = $this->get('phlexible_media_template.preview.audio');
        $data = $previewer->create($params);

        return new ResultResponse(true, 'Preview created', $data);
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/video", name="mediatemplates_preview_video")
     */
    public function videoAction(Request $request)
    {
        $params = $request->request->all();

        unset($params['module']);
        unset($params['controller']);
        unset($params['action']);

        $previewer = $this->get('phlexible_media_template.preview.video');
        $data = $previewer->create($params);

        return new ResultResponse(true, 'Preview created', $data);
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/pdf", name="mediatemplates_preview_pdf")
     */
    public function pdfAction(Request $request)
    {
        $params = $request->request->all();

        unset($params['module']);
        unset($params['controller']);
        unset($params['action']);

        $previewer = $this->get('phlexible_media_template.preview.pdf');
        $data = $previewer->create($params);

        return new ResultResponse(true, 'Preview created', $data);
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @Route("/get", name="mediatemplates_preview_get")
     */
    public function getAction(Request $request)
    {
        $filename = $request->get('file');
        $filename = $this->container->getParameter('phlexible_media_template.preview.temp_dir') . basename($filename);

        $mimeType = $this->get('phlexible_media_tool.mime.detector')->detect($filename, 'string');

        return new Response(file_get_contents($filename), 200, array('Content-type' => $mimeType));
    }
}
