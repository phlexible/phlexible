<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Event;

use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Symfony\Component\EventDispatcher\Event;

/**
 * Element version event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementVersionEvent extends Event
{
    /**
     * @var ElementVersion
     */
    private $elementVersion;

    /**
     * @param ElementVersion $elementVersion
     */
    public function __construct(ElementVersion $elementVersion)
    {
        $this->elementVersion = $elementVersion;
    }

    /**
     * @return ElementVersion
     */
    public function getElementVersion()
    {
        return $this->elementVersion;
    }
}
