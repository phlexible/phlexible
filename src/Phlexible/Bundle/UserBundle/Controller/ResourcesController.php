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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resources controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ResourcesController extends Controller
{
    /**
     * Return user javascripts
     *
     * @return Response
     */
    public function scriptsAction()
    {
        $locator = $this->get('file_locator');

        $content =
            file_get_contents($locator->locate('@PhlexibleUserBundle/Resources/scripts/ChangePasswordWindow.js')) .
            file_get_contents($locator->locate('@PhlexibleUserBundle/Resources/scripts/ValidateWindow.js')) .
            file_get_contents($locator->locate('@PhlexibleUserBundle/Resources/scripts/SetPasswordWindow.js'));

        return new Response($content, 200, ['Content-Type' => 'text/javascript']);
    }

    /**
     * Return user stylesheets
     *
     * @return Response
     */
    public function stylesAction()
    {
        $content = file_get_contents($locator->locate('@PhlexibleUserBundle/Resources/styles/users.css'));

        return new Response($content, 200, ['Content-Type' => 'text/css']);
    }

    /**
     * Return users icons
     *
     * @return Response
     */
    public function iconsAction()
    {
        $content = '';

        return new Response($content, 200, ['Content-Type' => 'text/css']);
    }

    /**
     * Return users translations
     *
     * @param string $language
     *
     * @return Response
     */
    public function translationsAction($language)
    {
        $language = $this->getUser()->getInterfaceLanguage($language);

        $translations = $this->get('resourcesTranslations');
        $content = $translations->get($language);

        return new Response($content, 200, ['Content-Type' => 'text/javascript']);
    }
}

