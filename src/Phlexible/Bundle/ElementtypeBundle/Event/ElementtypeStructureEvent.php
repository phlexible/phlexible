<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Event;

use Phlexible\Bundle\ElementtypeBundle\ElementtypeStructure\ElementtypeStructure;
use Symfony\Component\EventDispatcher\Event;

/**
 * Elementtype structure event
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class ElementtypeStructureEvent extends Event
{
    /**
     * @var ElementtypeStructure
     */
    private $elementtypeStructure;

    /**
     * @param ElementtypeStructure $elementtypeStructure
     */
    public function __construct(ElementtypeStructure $elementtypeStructure)
    {
        $this->elementtypeStructure = $elementtypeStructure;
    }

    /**
     * @return ElementtypeStructure
     */
    public function getElementtypeStructure()
    {
        return $this->elementtypeStructure;
    }
}