<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Model;

use Phlexible\Bundle\ElementBundle\Entity\Element;

/**
 * Element manager interface
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
     * @param array      $criteria
     * @param array|null $orderBy
     *
     * @return Element
     */
    public function findOneBy(array $criteria, $orderBy = null);

    /**
     * Update element
     *
     * @param Element $element
     * @param bool    $flush
     *
     * @return $this
     */
    public function updateElement(Element $element, $flush = true);

    /**
     * Delete element
     *
     * @param Element $element
     *
     * @return $this
     */
    public function deleteElement(Element $element);
}
