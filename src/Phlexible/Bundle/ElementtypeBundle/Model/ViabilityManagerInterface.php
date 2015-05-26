<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Model;

use Phlexible\Bundle\ElementtypeBundle\Entity\ElementtypeApply;

/**
 * Viability manager
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
     * Save viability
     *
     * @param Elementtype $elementtype
     * @param array       $parentIds
     *
     * @return $this
     */
    public function updateViability(Elementtype $elementtype, array $parentIds);
}
