<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Model;

use Phlexible\Bundle\ElementtypeBundle\Entity\Elementtype;

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
     * @return array
     */
    public function getAllowedParentIds(Elementtype $elementtype);

    /**
     * @param Elementtype $elementtype
     *
     * @return array
     */
    public function getAllowedChildrenIds(Elementtype $elementtype);

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
