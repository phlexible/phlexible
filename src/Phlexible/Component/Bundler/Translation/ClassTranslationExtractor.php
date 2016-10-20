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
 * Class based translation extractor
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class ClassTranslationExtractor implements TranslationExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function extract(MessageCatalogueInterface $catalogue, $domain)
    {
        $translations = array();

        $all = $catalogue->all($domain);
        foreach ($all as $key => $value) {
            $explodedKey = explode('.', $key);
            $key = array_pop($explodedKey);
            $class = implode('.', $explodedKey);
            $translations[$class][$key] = $value;
        }

        return $translations;
    }
}
