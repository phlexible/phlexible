<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\EventListener;

use Phlexible\Bundle\GuiBundle\Event\GetConfigEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Get config listener.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GetConfigListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var array
     */
    private $availableLanguages;

    /**
     * @var string
     */
    private $defaultLanguage;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param string                $availableLanguages
     * @param string                $defaultLanguage
     */
    public function __construct(TokenStorageInterface $tokenStorage, $availableLanguages, $defaultLanguage)
    {
        $this->tokenStorage = $tokenStorage;
        $this->availableLanguages = explode(',', $availableLanguages);
        $this->defaultLanguage = $defaultLanguage;
    }

    /**
     * @param GetConfigEvent $event
     */
    public function onGetConfig(GetConfigEvent $event)
    {
        $config = $event->getConfig();

        $sets = [];

        $languages = [];
        foreach ($this->availableLanguages as $key => $language) {
            $name = \Locale::getDisplayName($language, $this->tokenStorage->getToken()->getUser()->getInterfaceLanguage('en'));
            $languages[$name] = $language;
            unset($languages[$key]);
        }

        ksort($languages);

        foreach ($languages as $languageTitle => $language) {
            $sets['backendLanguages'][] = [
                $language,
                $languageTitle,
                'p-gui-'.$language.'-icon',
            ];
        }

        $sets['themes'] = [
            ['default', 'Default Theme', 'theme_default.png'],
            ['gray', 'Gray Theme', 'theme_gray.png'],
            //            array('slate', 'Slate Theme', 'theme_slate.png'),
            //            array('slickness', 'Slickness Theme', 'theme_slate.png'),
        ];

        $sets['dateFormats'] = [
            ['Y-m-d H:i:s', date('Y-m-d H:i:s').' (Y-m-d H:i:s)'],
            ['d.m.Y H:i:s', date('d.m.Y H:i:s').' (d.m.Y H:i:s)'],
        ];

        $config
            ->set('set.language.backend', $sets['backendLanguages'])
            ->set('set.themes', $sets['themes'])
            ->set('set.dateFormats', $sets['dateFormats'])
            ->set('language.backend', $this->defaultLanguage);
    }
}
