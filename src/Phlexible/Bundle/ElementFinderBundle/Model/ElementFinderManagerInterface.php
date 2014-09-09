<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\Model;

use Phlexible\Bundle\ElementFinderBundle\Entity\ElementFinderConfig;

/**
 * Element finder manager interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ElementFinderManagerInterface
{
    /**
     * @param int $id
     *
     * @return ElementFinderConfig
     */
    public function findCatch($id);

    /**
     * @param ElementFinderConfig $finderConfig
     */
    public function updateCatch(ElementFinderConfig $finderConfig);

    /**
     * @param ElementFinderConfig $finderConfig
     */
    public function deleteCatch(ElementFinderConfig $finderConfig);
}