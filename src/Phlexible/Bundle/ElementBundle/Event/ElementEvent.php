<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Event;

use Phlexible\Bundle\ElementBundle\Entity\Element;
use Symfony\Component\EventDispatcher\Event;

/**
 * Element event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementEvent extends Event
{
    /**
     * @var Element
     */
    private $element;

    /**
     * @param Element $element
     */
    public function __construct(Element $element)
    {
        $this->element = $element;
    }

    /**
     * @return Element
     */
    public function getElement()
    {
        return $this->element;
    }
}