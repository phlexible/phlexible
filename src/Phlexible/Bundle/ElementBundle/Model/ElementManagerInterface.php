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

/**
 * Element manager interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ElementManagerInterface
{
    /**
     * @param string $id
     *
     * @return Element
     */
    public function find($id);

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return Element[]
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null);

    /**
     * Update element.
     *
     * @param Element $element
     * @param bool    $flush
     *
     * @return $this
     */
    public function updateElement(Element $element, $flush);

    /**
     * Delete element.
     *
     * @param Element $element
     *
     * @return $this
     */
    public function deleteElement(Element $element);
}
