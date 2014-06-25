<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Event;

use Phlexible\Bundle\ElementtypeBundle\ElementtypeVersion\ElementtypeVersion;
use Symfony\Component\EventDispatcher\Event;

/**
 * Elementtype version event
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class ElementtypeVersionEvent extends Event
{
    /**
     * @var ElementtypeVersion
     */
    private $elementtypeVersion;

    /**
     * @param ElementtypeVersion $elementtypeVersion
     */
    public function __construct(ElementtypeVersion $elementtypeVersion)
    {
        $this->elementtypeVersion = $elementtypeVersion;
    }

    /**
     * @return ElementtypeVersion
     */
    public function getElementtypeVersion()
    {
        return $this->elementtypeVersion;
    }
}