<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTypeBundle\Controller;

use Phlexible\Component\MediaType\Compiler\CssCompiler;
use Phlexible\Component\MediaType\Compiler\ScriptCompiler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Asset controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/mediatypes/asset")
 * @Security("is_granted('ROLE_MEDIA_TYPES')")
 */
class AssetController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     * @Route("/scripts", name="mediatypes_asset_scripts")
     */
    public function scriptsAction(Request $request)
    {
        $mediaTypeManager = $this->get('phlexible_media_type.media_type_manager');

        $compiler = new ScriptCompiler();

        $scripts = $compiler->compile($mediaTypeManager->getCollection());

        return new Response($scripts, 200, array('Content-type' => 'text/javascript'));
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @Route("/css", name="mediatypes_asset_css")
     */
    public function cssAction(Request $request)
    {
        $mediaTypeManager = $this->get('phlexible_media_type.media_type_manager');
        $basePath = $request->getBasePath();
        $baseUrl = $request->getBaseUrl();

        $compiler = new CssCompiler();

        $css = $compiler->compile($mediaTypeManager->getCollection());
        $css = str_replace(
            ['/makeweb/', '/BASEPATH/', '/BASEURL/', '/COMPONENTSPATH/'],
            [$basePath, $basePath, $baseUrl, $basePath . 'bundles/'],
            $css
        );

        return new Response($css, 200, array('Content-type' => 'text/css'));
    }
}
