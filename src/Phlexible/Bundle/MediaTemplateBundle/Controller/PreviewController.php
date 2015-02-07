<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
            $params['width'] = 100;
        }
        if (empty($params['height'])) {
            $params['height'] = 100;
        }
        if (empty($params['xmethod'])) {
            $params['xmethod'] = 'fit';
        }

        $previewer = $this->get('phlexible_media_template.previewer.image');
        $locator = $this->get('file_locator');

        $previewImage = 'test_1000_600.jpg';
        if (isset($params['preview_image'])) {
            $previewImage = $params['preview_image'];
            unset($params['preview_image']);
            if ($previewImage === '800_600') {
                $previewImage = "test_$previewImage.png";
            } else {
                $previewImage = "test_$previewImage.jpg";
            }
        }

        $filePath = $locator->locate("@PhlexibleMediaTemplateBundle/Resources/public/images/$previewImage", null, true);
        $data = $previewer->create($filePath, $params);

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

        $previewer = $this->get('phlexible_media_template.previewer.audio');
        $locator = $this->get('file_locator');

        $filePath = $locator->locate('@PhlexibleMediaTemplateBundle/Resources/public/audio/test.mp3', null, true);
        $data = $previewer->create($filePath, $params);

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

        $previewer = $this->get('phlexible_media_template.previewer.video');
        $locator = $this->get('file_locator');

        $filePath = $locator->locate('@PhlexibleMediaTemplateBundle/Resources/public/video/test.mpg', null, true);
        $data = $previewer->create($filePath, $params);

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
        $filename = $this->container->getParameter('phlexible_media_template.previewer.temp_dir') . basename($filename);

        $mimeType = $this->get('phlexible_media_tool.mime.detector')->detect($filename, 'string');

        return new Response(file_get_contents($filename), 200, ['Content-type' => $mimeType]);
    }
}
