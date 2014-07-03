<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementRendererBundle\RenderConfigurator;

use Phlexible\Bundle\ElementRendererBundle\RenderConfiguration;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Locale configurator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LocaleConfigurator implements ConfiguratorInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param LoggerInterface          $logger
     */
    public function __construct(EventDispatcherInterface $dispatcher, LoggerInterface $logger)
    {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(Request $request, RenderConfiguration $renderConfiguration)
    {
        // Before Init Event
        /*
        $beforeEvent = new \Makeweb_Renderers_Event_BeforeInitLocale($this);
        if (!$this->dispatcher->dispatch($beforeEvent))
        {
            return false;
        }
        */

        $language = $request->attributes->get('language');

        try {
            if ($language === 'en') {
                $language = 'us';

                $context = $renderRequest->getContext();
                if ($context) {
                    $country = $context->getCountry();
                    if ('gb' === $country) {
                        $language = $country;
                    }
                }
            }

            $locale = \Zend_Locale::findLocale($language);
        } catch (\Exception $e) {
            $locale = 'en_US';
        }


        if (strpos($locale, '_') === false) {
            $locale = $locale . '_' . strtoupper($locale);
        }

        //if ($locale === 'tr_TR') $locale = 'en_EN';
        //$this->_locale = 'en_EN.utf8';
        // Test locale
        $setLocale = \setlocale(LC_COLLATE, $locale . '.utf8', $locale . '.utf-8');

        if ($setLocale) {
            // Set remaining locales

            \setlocale(LC_MESSAGES, $locale . '.utf8', $locale . '.utf-8');
            \setlocale(LC_MONETARY, $locale . '.utf8', $locale . '.utf-8');
            \setlocale(LC_NUMERIC, $locale . '.utf8', $locale . '.utf-8');
            \setlocale(LC_TIME, $locale . '.utf8', $locale . '.utf-8');
            if ($locale !== 'tr_TR') {
                // There seems to be a bug on debian systems with turkish locales, so we skip this one for turkish
                \setlocale(LC_CTYPE, $locale . '.utf8');
            }
        } else {
            // Fallback to non-utf8 locales

            $this->logger->notice('Can\'t set locale "' . $locale . '.utf8". Falling back to non-utf8.');

            \setlocale(LC_COLLATE, $locale);
            \setlocale(LC_TIME, $locale);

            if ($locale) {
                \setlocale(LC_MESSAGES, $locale);
                \setlocale(LC_MONETARY, $locale);
                \setlocale(LC_NUMERIC, $locale);
                if ($locale !== 'tr_TR') {
                    // There seems to be a bug on debian systems with turkish locales, so we skip this one for turkish
                    \setlocale(LC_CTYPE, $locale);
                }
            } else {
                $this->logger->err('Can\'t set locale "' . $locale . '". Locale is now undefined.');
            }
        }

        $zendLocale = new \Zend_Locale($locale);

        $renderConfiguration
            ->addFeature('locale')
            ->set('locale', $locale)
            ->set('zendLocale', $zendLocale);

        // Init Event
        /*
        $event = new \Makeweb_Renderers_Event_InitLocale($this);
        $this->dispatcher->dispatch($event);
        */
    }

}
