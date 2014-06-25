<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SecurityBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Asset controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/security/asset")
 */
class AssetController extends Controller
{
    /**
     * Return auth javascripts
     *
     * @return Response
     * @Route("/scripts", name="security_asset_scripts")
     */
    public function scriptsAction()
    {
        $content = '';
        $content .= file_get_contents(__DIR__ . '/../Resources/scripts/LoginWindow.js');
        $content .= file_get_contents(__DIR__ . '/../Resources/scripts/ValidateWindow.js');
        $content .= file_get_contents(__DIR__ . '/../Resources/scripts/ChangePasswordWindow.js');
        $content .= file_get_contents(__DIR__ . '/../Resources/scripts/SetPasswordWindow.js');

        return new Response($content, 200, array('Content-type' => 'text/javascript'));
    }

    /**
     * Return auth css
     *
     * @return Response
     * @Route("/css", name="security_asset_css")
     */
    public function cssAction()
    {
        $content = file_get_contents(__DIR__ . '/../Resources/styles/auth.css');

        return new Response($content, 200, array('Content-type' => 'text/css'));
    }

    /**
     * Return auth icons
     *
     * @param Request $request
     *
     * @return Response
     * @Route("/icons", name="security_asset_icons")
     */
    public function iconsAction(Request $request)
    {
        $content = '.p-security-login-icon {
    background-image:url(' . $request->getBasePath() . '/bundles/phlexiblesecurity/icons/login.png) !important;
}
';

        return new Response($content, 200, array('Content-type' => 'text/css'));
    }

    /**
     * Return auth translations
     *
     * @param string $language
     *
     * @return Response
     * @Route("/translations/{language}", name="security_asset_translations")
     */
    public function translationsAction($language)
    {
        $translationsBuilder = $this->get('phlexible_gui.asset.builder.translations');
        $content = $translationsBuilder->get($language, 'login');

        return new Response($content, 200, array('Content-type' => 'text/javascript'));
    }
}