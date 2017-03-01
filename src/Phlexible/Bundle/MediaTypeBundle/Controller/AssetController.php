<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaTypeBundle\Controller;

use Phlexible\Component\MediaType\Compiler\CssCompiler;
use Phlexible\Component\MediaType\Compiler\ScriptCompiler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Asset controller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/mediatypes/asset")
 * @Security("is_granted('ROLE_BACKEND')")
 */
class AssetController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     * @Route("/mediatypes.js", name="mediatypes_asset_scripts")
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
     * @Route("/mediatypes.css", name="mediatypes_asset_css")
     */
    public function cssAction(Request $request)
    {
        $mediaTypeManager = $this->get('phlexible_media_type.media_type_manager');
        $basePath = $request->getBasePath();
        $baseUrl = $request->getBaseUrl();

        $compiler = new CssCompiler();

        $css = $compiler->compile($mediaTypeManager->getCollection());
        $css = str_replace(
            ['/BASE_PATH/', '/BASE_URL/', '/BUNDLES_PATH/'],
            [$basePath, $baseUrl, $basePath.'bundles/'],
            $css
        );

        return new Response($css, 200, array('Content-type' => 'text/css'));
    }
}
