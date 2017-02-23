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

use Phlexible\Bundle\ElementtypeBundle\Entity\ElementtypeApply;

/**
 * Viability manager.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ViabilityManagerInterface
{
    /**
     * @param Elementtype $elementtype
     *
     * @return ElementtypeApply[]
     */
    public function findAllowedParents(Elementtype $elementtype);

    /**
     * @param Elementtype $elementtype
     *
     * @return ElementtypeApply[]
     */
    public function findAllowedChildren(Elementtype $elementtype);

    /**
     * Save viability.
     *
     * @param Elementtype $elementtype
     * @param array       $parentIds
     *
     * @return $this
     */
    public function updateViability(Elementtype $elementtype, array $parentIds);
}
