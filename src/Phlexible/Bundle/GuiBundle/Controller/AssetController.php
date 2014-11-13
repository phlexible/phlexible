<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Controller;

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
        $scriptsBuilder = $this->get('phlexible_gui.asset.builder.scripts');
        $content = $scriptsBuilder->get();

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
        $cssBuilder = $this->get('phlexible_gui.asset.builder.css');
        $content = $cssBuilder->get($request->getBaseUrl(), $request->getBasePath());

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

    /**
     * Create and deliver an image based on a translation string
     *
     * @param Request $request
     *
     * @return Response
     * @Route("/textimage", name="asset_textimage")
     */
    public function textimageAction(Request $request)
    {
        $key = $request->query->get('key', null);
        $text = $request->query->get('text', null);
        $color = $request->query->get('color', null);
        $location = $request->query->get('location', 'west');
        $component = $request->query->get('component', null);
        $icon = $request->query->get('icon', null);

        $user = $this->getUser();

        if (!$text) {
            if (!$key) {
                $this->createNotFoundException('Neither key nor text given.');
            }

            $translator = $this->get('translator');
            $text = html_entity_decode($translator->trans($key, [], 'gui', $user->getInterfaceLanguage('en')));
        }

        if (!$color) {
            $color = '7fa1cc';
        }
        $color = '#' . ltrim($color, '#');

        $iconPath = null;
        if ($component && $icon) {
            $class = $this->container->getParameter('kernel.bundles')[ucfirst($component) . 'Bundle'];
            $reflection = new \ReflectionClass($class);
            $iconPath = dirname($reflection->getFileName()) . '/Resources/public/icons/' . $icon . '.png';
        }

        $textimage = $this->get('phlexible_gui.asset.textimage.renderer');
        $textimageFile = $textimage->get($text, $color, $location, $iconPath);

        if (!$textimageFile) {
            return new Response('Error creating image.', 500);
        }

        return new Response(file_get_contents($textimageFile), 200, ['Content-type' => 'image/png']);
    }
}

