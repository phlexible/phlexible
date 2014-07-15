<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Config;

use Phlexible\Bundle\GuiBundle\Event\GetConfigEvent;
use Phlexible\Bundle\GuiBundle\GuiEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Config builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ConfigBuilder
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @var array
     */
    private $availableLanguages;

    /**
     * @var string
     */
    private $defaultLanguage;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param SecurityContextInterface $securityContext
     * @param string                   $availableLanguages
     * @param string                   $defaultLanguage
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        SecurityContextInterface $securityContext,
        $availableLanguages,
        $defaultLanguage)
    {
        $this->dispatcher = $dispatcher;
        $this->securityContext = $securityContext;
        $this->availableLanguages = explode(',', $availableLanguages);
        $this->defaultLanguage = $defaultLanguage;
    }

    /**
     * Gather configs and return config array
     *
     * @return array
     */
    public function toArray()
    {
        $sets = array();

        $languages = array();
        foreach ($this->availableLanguages as $key => $language) {
            $name = \Locale::getDisplayName($language, $this->securityContext->getToken()->getUser()->getInterfaceLanguage());
            $languages[$name] = $language;
            unset($languages[$key]);
        }

        ksort($languages);

        foreach ($languages as $languageTitle => $language) {
            $sets['backendLanguages'][] = array(
                $language,
                $languageTitle,
                'p-flags-' . $language . '-icon',
            );
        }

        $sets['themes'] = array(
            array('default', 'Default Theme', 'theme_default.png'),
            array('gray', 'Gray Theme', 'theme_gray.png'),
            //            array('slate', 'Slate Theme', 'theme_slate.png'),
            //            array('slickness', 'Slickness Theme', 'theme_slate.png'),
        );

        $sets['dateFormats'] = array(
            array('Y-m-d H:i:s', date('Y-m-d H:i:s') . ' (Y-m-d H:i:s)'),
            array('d.m.Y H:i:s', date('d.m.Y H:i:s') . ' (d.m.Y H:i:s)'),
        );

        $config = new Config();
        $config
            ->set('set.language.backend', $sets['backendLanguages'])
            ->set('set.themes', $sets['themes'])
            ->set('set.dateFormats', $sets['dateFormats'])
            ->set('language.backend', $this->defaultLanguage);

        $event = new GetConfigEvent($this->securityContext, $config);
        $this->dispatcher->dispatch(GuiEvents::GET_CONFIG, $event);

        return $config;
    }
}
