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

/**
 * Class translation builder
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class ClassTranslationBuilder implements TranslationBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function build(array $translations, $locale)
    {
        $template = 'Ext.define("%s", %s);';

        $content = '';
        foreach ($translations as $class => $values) {
            $values = array('override' => $class) + $values;
            $className = sprintf('Ext.locale.%s.%s', $locale, $class);
            $content .= sprintf($template, $className, json_encode($values, JSON_PRETTY_PRINT)).PHP_EOL;
        }

        return $content;
    }
}
