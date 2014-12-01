<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.makeweb.de/LICENCE     Dummy Licence
 */

namespace Phlexible\Bundle\GuiBundle\Translator;

use Symfony\Component\Translation\LoggingTranslator;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Translation catalog accessor
 *
 * @author Michael van Engelshoven <mve@brainbits.net>
 */
class CatalogAccessor
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Return catalogues for locale
     *
     * @param string $locale
     *
     * @return array
     */
    public function getCatalogues($locale)
    {
        if ($this->translator instanceof LoggingTranslator) {
            $reflectionClass = new \ReflectionClass($this->translator);
            $property = $reflectionClass->getProperty('translator');
            $property->setAccessible(true);
            $translator = $property->getValue($this->translator);
        } else {
            $translator = $this->translator;
        }

        $reflectionClass = new \ReflectionClass($translator);
        $method = $reflectionClass->getMethod('loadCatalogue');
        $method->setAccessible(true);
        $method->invoke($translator, $locale);

        $property = $reflectionClass->getProperty('catalogues');
        $property->setAccessible(true);

        $catalogues = $property->getValue($translator);

        return $catalogues[$locale];
    }
}
