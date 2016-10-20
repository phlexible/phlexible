<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\GuiAsset\Builder;

use Phlexible\Component\GuiAsset\Compressor\CompressorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\TranslatorBagInterface;

/**
 * Translations builder
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
     * @var CompressorInterface
     */
    private $javascriptCompressor;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @param TranslatorBagInterface $translator
     * @param CompressorInterface    $javascriptCompressor
     * @param string                 $cacheDir
     */
    public function __construct(
        TranslatorBagInterface $translator,
        CompressorInterface $javascriptCompressor,
        $cacheDir)
    {
        $this->translator = $translator;
        $this->javascriptCompressor = $javascriptCompressor;
        $this->cacheDir = $cacheDir;
    }

    /**
     * Get all Translations for the given section
     *
     * @param string $language
     * @param string $domain
     *
     * @return string
     */
    public function build($language, $domain = 'gui')
    {
        $translations = [];
        $catalogue = $this->translator->getCatalogue($language);
        $namespaces = [];
        foreach ($catalogue->all($domain) as $key => $value) {
            $parts = explode('.', $key);
            $component = array_shift($parts);
            $namespace = 'Phlexible.' . strtolower($component) . '.Strings';
            if (count($parts) > 1) {
                $key1 = array_shift($parts);
                $key2 = array_shift($parts);
                $namespaces[$namespace][$key1][$key2] = $value;
            } else {
                $key = array_shift($parts);
                $namespaces[$namespace][$key] = $value;
            }
        }
        foreach ($namespaces as $namespace => $keys) {
            $translations[$namespace] = $keys;
        }

        $cacheFilename = $this->cacheDir . '/translations-' . $language . '.js';

        $content = $this->buildTranslations($translations);

        $filesystem = new Filesystem();
        $filesystem->dumpFile($cacheFilename, $content);

        return $cacheFilename;
    }

    /**
     * Glue together all scripts and return file/memory stream
     *
     * @param array $languages
     *
     * @return string
     */
    private function buildTranslations(array $languages)
    {
        $namespaces = [];

        $content = '';
        foreach ($languages as $namespace => $page) {
            $parentNamespace = explode('.', $namespace);
            array_pop($parentNamespace);
            $parentNamespace = implode('.', $parentNamespace);

            if (!in_array($parentNamespace, $namespaces)) {
                $content .= 'Ext.namespace("' . $parentNamespace . '");' . PHP_EOL;
                $namespaces[] = $parentNamespace;
            }

            $content .= $namespace . ' = ' . json_encode($page) . ';' . PHP_EOL;
            $content .= $namespace . '.get = function(s){return this[s]};' . PHP_EOL;
        }

        $content = $this->compress($content);

        return $content;
    }

    /**
     * Javascript-aware compress the input string
     *
     * @param string $script
     *
     * @return string
     */
    private function compress($script)
    {
        return $this->javascriptCompressor->compressString($script);
    }
}
