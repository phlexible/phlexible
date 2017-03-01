<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Model;

use Phlexible\Bundle\ElementBundle\Entity\Element;
use Phlexible\Bundle\ElementBundle\Entity\ElementSource;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;

/**
 * Element version manager interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ElementVersionManagerInterface
{
    /**
     * @param Element $element
     * @param int     $version
     *
     * @return ElementVersion
     */
    public function find(Element $element, $version);

    /**
     * @param Element $element
     * @param array   $orderBy
     * @param int     $limit
     * @param int     $offset
     *
     * @return ElementVersion[]
     */
    public function findAllByElement(Element $element, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param ElementSource $elementSource
     *
     * @return ElementVersion[]
     */
    public function findByElementSource(ElementSource $elementSource);

    /**
     * @param ElementVersion $elementVersion
     * @param bool           $flush
     */
    public function updateElementVersion(ElementVersion $elementVersion, $flush = true);
}
