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

use Phlexible\Component\GuiAsset\Exception\InvalidArgumentException;

/**
 * Namespace translation builder
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class NamespaceTranslationBuilder implements TranslationBuilderInterface
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string
     */
    private $suffix;

    /**
     * @param string $prefix
     * @param string $suffix
     */
    public function __construct($prefix = 'Phlexible.', $suffix = '.Strings')
    {
        $this->prefix = $prefix;
        $this->suffix = $suffix;
    }

    /**
     * {@inheritdoc}
     */
    public function build(array $translations, $locale)
    {
        $namespaces = [];

        $content = '';
        foreach ($translations as $namespace => $page) {
            if (strpos($namespace, '.')) {
                throw new InvalidArgumentException("Namespace part $namespace can not contain dots.");
            }
            if (!is_array($page)) {
                throw new InvalidArgumentException("Page part $namespace has to be an array.");
            }

            $parentNamespace = $this->prefix.$namespace;
            $namespace = $parentNamespace.$this->suffix;

            if (!in_array($parentNamespace, $namespaces)) {
                $content .= 'Ext.namespace("' . $parentNamespace . '");' . PHP_EOL;
                $namespaces[] = $parentNamespace;
            }

            $content .= $namespace . ' = ' . json_encode($page) . ';' . PHP_EOL;
            $content .= $namespace . '.get = function(s){return this[s]};' . PHP_EOL;
        }

        return $content;
    }
}
