<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MetaSetBundle\EventListener;

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
     * @var string
     */
    private $defaultLanguage;

    /**
     * @var array
     */
    private $availableLanguages;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param string                $defaultLanguage
     * @param string                $availableLanguages
     */
    public function __construct(TokenStorageInterface $tokenStorage, $defaultLanguage, $availableLanguages)
    {
        $this->tokenStorage = $tokenStorage;
        $this->defaultLanguage = $defaultLanguage;
        $this->availableLanguages = explode(',', $availableLanguages);
    }

    /**
     * @param GetConfigEvent $event
     */
    public function onGetConfig(GetConfigEvent $event)
    {
        $config = $event->getConfig();

        $user = $this->tokenStorage->getToken()->getUser();
        $guiLanguage = $user->getInterfaceLanguage('en');

        $languages = [];
        foreach ($this->availableLanguages as $language) {
            $name = \Locale::getDisplayName($language, $guiLanguage);
            $languages[$name] = $language;
        }

        ksort($languages);

        $metaLanguages = [];
        foreach ($languages as $languageTitle => $language) {
            $metaLanguages[] = [
                $language,
                $languageTitle,
                'p-gui-'.$language.'-icon',
            ];
        }

        $config->set('language.metasets', $this->defaultLanguage);
        $config->set('set.language.meta', $metaLanguages);
    }
}
