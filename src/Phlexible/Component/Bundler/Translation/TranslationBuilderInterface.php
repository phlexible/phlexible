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
 * Translation builder interface
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
interface TranslationBuilderInterface
{
    /**
     * @param array  $translations
     * @param string $locale
     *
     * @return array
     */
    public function build(array $translations, $locale);
}
