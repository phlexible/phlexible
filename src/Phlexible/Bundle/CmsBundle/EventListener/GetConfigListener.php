<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\CmsBundle\EventListener;

use Phlexible\Bundle\GuiBundle\Event\GetConfigEvent;

/**
 * Get config listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GetConfigListener
{
    /**
     * @var string
     */
    private $defaultLanguage;

    /**
     * @var array
     */
    private $availableLanguages;

    /**
     * @param string $defaultLanguage
     * @param string $availableLanguages
     */
    public function __construct($defaultLanguage, $availableLanguages)
    {
        $this->defaultLanguage = $defaultLanguage;
        $this->availableLanguages = explode(',', $availableLanguages);
    }

    /**
     * @param GetConfigEvent $event
     */
    public function onGetConfig(GetConfigEvent $event)
    {
        $config = $event->getConfig();

        $languages = array();
        foreach ($this->availableLanguages as $language) {
            $name = \Zend_Locale::getTranslation($language, 'language', 'de');
            $languages[$name] = $language;
        }

        ksort($languages);

        $frontendLanguages = array();
        foreach ($languages as $languageTitle => $language) {
            $frontendLanguages[] = array(
                $language,
                $languageTitle,
                'p-flags-' . $language . '-icon',
            );
        }

        $config->set('language.frontend', $this->defaultLanguage);
        $config->set('set.language.frontend', $frontendLanguages);
    }
}
