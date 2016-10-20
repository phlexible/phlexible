<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\GuiAsset\Translation;

use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * Namespace translation extractor
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class NamespaceTranslationExtractor implements TranslationExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function extract(MessageCatalogueInterface $catalogue, $domain)
    {
        $namespaces = [];
        foreach ($catalogue->all($domain) as $key => $value) {
            $this->unflatten($namespaces, $key, $value);
        }

        return $namespaces;
    }

    /**
     * @param array  $arr
     * @param string $path
     * @param mixed  $value
     */
    function unflatten(array &$arr, $path, $value)
    {
        $parts = explode('.', $path);

        foreach ($parts as $part) {
            $arr = &$arr[$part];
        }

        $arr = $value;
    }
}
