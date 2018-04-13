<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MetaSet\Model;

use Symfony\Component\Translation\TranslatorInterface;

/**
 * Option resolver.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class OptionResolver
{
    private $translator;
    private $languages;
    private $domain;
    private $useTitlePrefix;

    /**
     * OptionResolver constructor.
     *
     * @param TranslatorInterface $translator
     * @param string              $languages
     * @param string              $domain
     * @param bool                $useTitlePrefix
     */
    public function __construct(TranslatorInterface $translator, $languages, $domain = 'meta', $useTitlePrefix = false)
    {
        $this->translator = $translator;
        $this->languages = explode(',', $languages);
        $this->domain = $domain;
        $this->useTitlePrefix = $useTitlePrefix;
    }

    /**
     * @param MetaSetField $field
     *
     * @return null|array
     */
    public function resolve(MetaSetField $field)
    {
        $type = $field->getType();

        if ($type === 'select') {
            $options = [];
            foreach (explode(',', $field->getOptions()) as $value) {
                $option = [$value];

                $translationIdentifier = $value;
                if ($this->useTitlePrefix) {
                    $translationIdentifier = $field->getMetaSet()->getName().'.'.$translationIdentifier;
                }

                foreach ($this->languages as $language) {
                    $option[] = $this->translator->trans($translationIdentifier, [], $this->domain, $language);
                }

                $options[] = $option;
            }

            return $options;
        } elseif ($type === 'suggest') {
            $dataSourceId = $field->getOptions();
            $options = [
                'source_id' => $dataSourceId,
            ];

            return $options;
        }

        return null;
    }
}
