<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\ContentElement\Loader;

use Phlexible\Bundle\ElementBundle\ContentElement\ContentElement;

/**
 * Loader interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface LoaderInterface
{
    /**
     * @param int    $eid
     * @param int    $version
     * @param string $language
     *
     * @return ContentElement
     */
    public function load($eid, $version, $language);
}
