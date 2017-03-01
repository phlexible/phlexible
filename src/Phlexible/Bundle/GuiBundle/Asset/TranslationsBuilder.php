<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Asset;

use Phlexible\Component\Bundler\Asset\Asset;
use Phlexible\Component\Bundler\Compressor\CompressorInterface;
use Phlexible\Component\Bundler\Content\ResourceContent;
use Phlexible\Component\Bundler\Translation\TranslationBuilderInterface;
use Phlexible\Component\Bundler\Translation\TranslationExtractorInterface;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Translation\TranslatorBagInterface;

/**
 * Translations builder.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TranslationsBuilder
{
    /**
     * @var TranslatorBagInterface
     */
    private $translator;

    /**
     * @var TranslationExtractorInterface
     */
    private $extractor;

    /**
     * @var TranslationBuilderInterface
     */
    private $builder;

    /**
     * @var CompressorInterface
     */
    private $compressor;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var string
     */
    private $fallbackLocale;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @param TranslatorBagInterface        $translator
     * @param TranslationExtractorInterface $extractor
     * @param TranslationBuilderInterface   $builder
     * @param CompressorInterface           $compressor
     * @param string                        $cacheDir
     * @param string                        $fallbackLocale
     * @param bool                          $debug
     */
    public function __construct(
        TranslatorBagInterface $translator,
        TranslationExtractorInterface $extractor,
        TranslationBuilderInterface $builder,
        CompressorInterface $compressor,
        $cacheDir,
        $fallbackLocale,
        $debug
    ) {
        $this->translator = $translator;
        $this->extractor = $extractor;
        $this->builder = $builder;
        $this->compressor = $compressor;
        $this->cacheDir = $cacheDir;
        $this->fallbackLocale = $fallbackLocale;
        $this->debug = $debug;
    }

    /**
     * Get all Translations for the given section.
     *
     * @param string $locale
     * @param string $domain
     *
     * @return Asset
     */
    public function build($locale, $domain = 'gui')
    {
        $cache = new ConfigCache($this->cacheDir.'/translations-'.$locale.'.js', $this->debug);

        if (!$cache->isFresh()) {
            $content = $this->buildContent($locale, $domain);

            $cache->write($content->getContent(), $content->getResources());

            if (!$this->debug) {
                $this->compressor->compressFile((string) $cache);
            }
        }

        return new Asset((string) $cache);
    }

    /**
     * @param string $locale
     * @param string $domain
     *
     * @return ResourceContent
     */
    private function buildContent($locale, $domain)
    {
        $translations = [];
        $resources = [];

        if ($locale !== $this->fallbackLocale) {
            $fallbackCatalogue = $this->translator->getCatalogue($this->fallbackLocale);
            $resources = array_merge($resources, $fallbackCatalogue->getResources());
            $translations = array_merge($translations, $this->extractor->extract($fallbackCatalogue, $domain));
        }

        $catalogue = $this->translator->getCatalogue($locale);
        $resources = array_merge($resources, $catalogue->getResources());
        $translations = array_merge($translations, $this->extractor->extract($catalogue, $domain));

        $content = $this->builder->build($translations, $locale);

        return new ResourceContent($content, $resources);
    }
}
