<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Asset controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/users/asset")
 */
class AssetController extends Controller
{
    /**
     * Return auth javascripts
     *
     * @return Response
     * @Route("/scripts", name="phlexible_user_asset_scripts")
     */
    public function scriptsAction()
    {
        $content = '';
        $content .= file_get_contents(__DIR__ . '/../Resources/scripts/security/LoginWindow.js');
        $content .= file_get_contents(__DIR__ . '/../Resources/scripts/security/SendEmailWindow.js');
        $content .= file_get_contents(__DIR__ . '/../Resources/scripts/security/ResetWindow.js');

        return new Response($content, 200, ['Content-type' => 'text/javascript']);
    }
}
