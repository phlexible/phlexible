<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Asset controller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/gui/asset")
 */
class AssetController extends Controller
{
    /**
     * Output scripts.
     *
     * @return Response
     * @Route("/gui.js", name="asset_scripts")
     */
    public function scriptsAction()
    {
        $scriptsBuilder = $this->get('phlexible_gui.asset.scripts_builder');
        $asset = $scriptsBuilder->build();

        $response = new BinaryFileResponse($asset, 200, array('Content-Type' => 'text/javascript;charset=UTF-8'));
        if ($asset->getMapAsset()) {
            $response->headers->set('X-SourceMap', $this->generateUrl('asset_scripts_map'));
        }

        return $response;
    }

    /**
     * Output scripts map.
     *
     * @return Response
     * @Route("/gui.js.map", name="asset_scripts_map")
     */
    public function scriptsMapAction()
    {
        $scriptsBuilder = $this->get('phlexible_gui.asset.scripts_builder');
        $asset = $scriptsBuilder->build();

        return new BinaryFileResponse($asset->getMapAsset(), 200, array('Content-Type' => 'application/json;charset=UTF-8'));
    }

    /**
     * Output css.
     *
     * @param Request $request
     *
     * @return Response
     * @Route("/gui.css", name="asset_css")
     */
    public function cssAction(Request $request)
    {
        $baseUrlContentFilter = $this->get('phlexible_gui.asset.base_url_content_filter');
        $baseUrlContentFilter->setBasePath($request->getBasePath());
        $baseUrlContentFilter->setBaseUrl($request->getBaseUrl());

        $cssBuilder = $this->get('phlexible_gui.asset.css_builder');

        $asset = $cssBuilder->build();

        $response = new BinaryFileResponse($asset, 200, array('Content-Type' => 'text/css;charset=UTF-8'));
        if ($asset->getMapAsset()) {
            $response->headers->set('X-SourceMap', $this->generateUrl('asset_css_map'));
        }

        return $response;
    }

    /**
     * Output css map.
     *
     * @param Request $request
     *
     * @return Response
     * @Route("/gui.css.map", name="asset_css_map")
     */
    public function cssMapAction(Request $request)
    {
        $baseUrlContentFilter = $this->get('phlexible_gui.asset.base_url_content_filter');
        $baseUrlContentFilter->setBasePath($request->getBasePath());
        $baseUrlContentFilter->setBaseUrl($request->getBaseUrl());

        $cssBuilder = $this->get('phlexible_gui.asset.css_builder');

        $asset = $cssBuilder->build();

        return new BinaryFileResponse($asset->getMapAsset(), 200, array('Content-Type' => 'application/json;charset=UTF-8'));
    }

    /**
     * Output icon styles.
     *
     * @param Request $request
     *
     * @return Response
     * @Route("/icons", name="asset_icons")
     */
    public function iconsAction(Request $request)
    {
        $iconsBuilder = $this->get('phlexible_gui.asset.icons_builder');
        $asset = $iconsBuilder->build($request->getBasePath());

        return new BinaryFileResponse($asset, 200, array('Content-Type' => 'text/css;charset=UTF-8'));
    }

    /**
     * Output translations.
     *
     * @param Request $request
     * @param string  $language
     *
     * @return Response
     * @Route("/translations/{language}", name="asset_translations")
     */
    public function translationsAction(Request $request, $language)
    {
        $translationBuilder = $this->get('phlexible_gui.asset.translations_builder');
        $asset = $translationBuilder->build($language);

        return new BinaryFileResponse($asset, 200, array('Content-Type' => 'text/javascript;charset=UTF-8'));
    }
}
