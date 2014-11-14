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
        $locator = $this->get('file_locator');

        $content =
            file_get_contents($locator->locate('@PhlexibleUserBundle/Resources/scripts/security/LoginWindow.js')) .
            file_get_contents($locator->locate('@PhlexibleUserBundle/Resources/scripts/security/SendEmailWindow.js')) .
            file_get_contents($locator->locate('@PhlexibleUserBundle/Resources/scripts/security/ResetWindow.js'));

        return new Response($content, 200, ['Content-type' => 'text/javascript']);
    }
}
