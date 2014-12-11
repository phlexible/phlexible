<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Controller;

use Puli\Repository\Filesystem\PhpCacheRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Asset controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/gui/asset")
 */
class AssetController extends Controller
{
    /**
     * Output scripts
     *
     * @return Response
     * @Route("/scripts", name="asset_scripts")
     */
    public function scriptsAction()
    {
        $repo = new PhpCacheRepository($this->container->getParameter('kernel.root_dir') . '/../.puli/dump');

        $scriptsBuilder = $this->get('phlexible_gui.asset.builder.scripts');
        $content = $scriptsBuilder->get($repo);

        return new Response($content, 200, ['Content-type' => 'text/javascript']);
    }

    /**
     * Output css
     *
     * @param Request $request
     *
     * @return Response
     * @Route("/css", name="asset_css")
     */
    public function cssAction(Request $request)
    {
        $repo = new PhpCacheRepository($this->container->getParameter('kernel.root_dir') . '/../.puli/dump');

        $cssBuilder = $this->get('phlexible_gui.asset.builder.css');
        $content = $cssBuilder->get($request->getBaseUrl(), $request->getBasePath(), $repo);

        return new Response($content, 200, ['Content-type' => 'text/css']);
    }

    /**
     * Output icon styles
     *
     * @param Request $request
     *
     * @return Response
     * @Route("/icons", name="asset_icons")
     */
    public function iconsAction(Request $request)
    {
        $iconsBuilder = $this->get('phlexible_gui.asset.builder.icons');
        $content = $iconsBuilder->get($request->getBaseUrl(), $request->getBasePath());

        return new Response($content, 200, ['Content-type' => 'text/css']);
    }

    /**
     * Output translations
     *
     * @param Request $request
     * @param string  $language
     *
     * @return Response
     * @Route("/translations/{language}", name="asset_translations")
     */
    public function translationsAction(Request $request, $language)
    {
        $translationBuilder = $this->get('phlexible_gui.asset.builder.translations');
        $content = $translationBuilder->get($language);

        return new Response($content, 200, ['Content-type' => 'text/javascript']);
    }
}

