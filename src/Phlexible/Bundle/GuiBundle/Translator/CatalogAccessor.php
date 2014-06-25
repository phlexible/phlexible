<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.makeweb.de/LICENCE     Dummy Licence
 */

namespace Phlexible\Bundle\GuiBundle\Translator;

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
        $this->loadCatalogue($locale);
        $reflectionClass = new \ReflectionClass($this->translator);
        $property = $reflectionClass->getProperty('catalogues');
        $property->setAccessible(true);

        $catalogues = $property->getValue($this->translator);

        return $catalogues[$locale];
    }

    /**
     * Load catalogue
     *
     * @param string $locale
     *
     * @return MessageCatalogueInterface
     */
    private function loadCatalogue($locale)
    {
        $reflection = new \ReflectionClass($this->translator);
        $method = $reflection->getMethod('loadCatalogue');
        $method->setAccessible(true);

        return $method->invoke($this->translator, $locale);
    }
}
