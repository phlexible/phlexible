<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Event;

use Phlexible\Bundle\ElementBundle\Model\ElementStructure;
use Symfony\Component\EventDispatcher\Event;

/**
 * Element structure event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementStructureEvent extends Event
{
    /**
     * @var ElementStructure
     */
    private $elementStructure;

    /**
     * @param ElementStructure $elementStructure
     */
    public function __construct(ElementStructure $elementStructure)
    {
        $this->elementStructure = $elementStructure;
    }

    /**
     * @return ElementStructure
     */
    public function getElementStructure()
    {
        return $this->elementStructure;
    }
}
