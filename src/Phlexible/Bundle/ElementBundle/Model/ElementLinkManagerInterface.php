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

use Phlexible\Bundle\ElementBundle\Entity\ElementLink;

/**
 * Element link manager interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ElementLinkManagerInterface
{
    /**
     * @param string $id
     *
     * @return ElementLink
     */
    public function find($id);

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return ElementLink[]
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null);

    /**
     * Update element link.
     *
     * @param ElementLink $elementLink
     * @param bool        $flush
     */
    public function updateElementLink(ElementLink $elementLink, $flush = true);

    /**
     * Update element links.
     *
     * @param ElementLink[] $elementLinks
     * @param bool          $flush
     */
    public function updateElementLinks(array $elementLinks, $flush = true);

    /**
     * Delete element link.
     *
     * @param ElementLink $elementLink
     */
    public function deleteElementLink(ElementLink $elementLink);
}
