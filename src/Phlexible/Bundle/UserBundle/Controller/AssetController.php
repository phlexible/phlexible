<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
