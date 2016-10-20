<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementtypeBundle\SelectFieldProvider;

/**
 * Select field provider interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface SelectFieldProviderInterface
{
    /**
     * Return name of this provider
     *
     * @return string
     */
    public function getName();

    /**
     * Return title for this provider
     *
     * @param string $language
     *
     * @return string
     */
    public function getTitle($language);

    /**
     * Return associative data for this provider
     *
     * @param string $siterootId
     * @param string $interfaceLanguage
     * @param string $language
     *
     * @return array
     */
    public function getData($siterootId, $interfaceLanguage, $language);
}
