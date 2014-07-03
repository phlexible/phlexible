<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Frontend bundle
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleFrontendBundle extends Bundle
{
    public function getFrontendPublishItems()
    {
        return $this->getContainer()->get('frontendFrontendPublishItems')->getItems();
    }

    /*
    public function getFrontendRoutes()
    {
        $frontendOnline = new RegexRoute(
            '(.*)',
            array('module' => 'frontend', 'controller' => 'online', 'action' => 'index')
        );

        $frontendFavicon = new Route(
            '/favicon.ico',
            array('module' => 'frontend', 'controller' => 'include', 'action' => 'favicon')
        );

        $frontendStyle = new RegexRoute(
            'styles/(.*)',
            array('module' => 'frontend', 'controller' => 'include', 'action' => 'style'),
            array(1 => 'path'),
            'styles/%s'
        );

        $frontendScripts = new RegexRoute(
            'scripts/(.*)',
            array('module' => 'frontend', 'controller' => 'include', 'action' => 'script'),
            array(1 => 'path'),
            'scripts/%s'
        );

        $frontendStatic = new RegexRoute(
            'static/(.*)',
            array('module' => 'frontend', 'controller' => 'include', 'action' => 'static'),
            array(1 => 'path'),
            'static/%s'
        );

        return array(
            'default'          => $frontendOnline,
            'frontend_favicon' => $frontendFavicon,
            'frontend_style'   => $frontendStyle,
            'frontend_script'  => $frontendScripts,
            'frontend_static'  => $frontendStatic,
        );
    }
    */
}
