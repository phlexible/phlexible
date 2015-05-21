<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Elementtype\Model;

use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Elementtype manager interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ElementtypeManagerInterface
{
    /**
     * Find element type by ID
     *
     * @param int $elementtypeId
     *
     * @return Elementtype
     */
    public function find($elementtypeId);

    /**
     * Find all element types
     *
     * @return Elementtype[]
     */
    public function findAll();

    /**
     * Validate element type
     *
     * @param Elementtype $elementtype
     *
     * @return null|ConstraintViolationListInterface
     */
    public function validateElementtype(Elementtype $elementtype);

    /**
     * Save element type
     *
     * @param Elementtype $elementtype
     */
    public function updateElementtype(Elementtype $elementtype);

    /**
     * Delete an element type
     *
     * @param Elementtype $elementtype
     */
    public function deleteElementtype(Elementtype $elementtype);
}
