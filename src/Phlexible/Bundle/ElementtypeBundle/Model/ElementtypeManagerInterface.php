<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementtypeBundle\Model;

use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Elementtype manager interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ElementtypeManagerInterface
{
    /**
     * Find element type by ID.
     *
     * @param int $elementtypeId
     *
     * @return Elementtype
     */
    public function find($elementtypeId);

    /**
     * Find all element types.
     *
     * @return Elementtype[]
     */
    public function findAll();

    /**
     * Validate element type.
     *
     * @param Elementtype $elementtype
     *
     * @return null|ConstraintViolationListInterface
     */
    public function validateElementtype(Elementtype $elementtype);

    /**
     * Save element type.
     *
     * @param Elementtype $elementtype
     */
    public function updateElementtype(Elementtype $elementtype);

    /**
     * Delete an element type.
     *
     * @param Elementtype $elementtype
     */
    public function deleteElementtype(Elementtype $elementtype);
}
