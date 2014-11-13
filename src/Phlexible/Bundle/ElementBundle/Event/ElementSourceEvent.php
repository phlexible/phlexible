<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Event;

use Phlexible\Bundle\ElementBundle\Entity\ElementSource;
use Symfony\Component\EventDispatcher\Event;

/**
 * Element source event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementSourceEvent extends Event
{
    /**
     * @var ElementSource
     */
    private $elementSource;

    /**
     * @param ElementSource $elementSource
     */
    public function __construct(ElementSource $elementSource)
    {
        $this->elementSource = $elementSource;
    }

    /**
     * @return ElementSource
     */
    public function getElementSource()
    {
        return $this->elementSource;
    }
}
