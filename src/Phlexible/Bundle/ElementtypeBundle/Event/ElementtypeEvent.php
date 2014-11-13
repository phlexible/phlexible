<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Event;

use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;
use Symfony\Component\EventDispatcher\Event;

/**
 * Elementtype event
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class ElementtypeEvent extends Event
{
    /**
     * @var Elementtype
     */
    private $elementtype;

    /**
     * @param Elementtype $elementtype
     */
    public function __construct(Elementtype $elementtype)
    {
        $this->elementtype = $elementtype;
    }

    /**
     * @return Elementtype
     */
    public function getElementtype()
    {
        return $this->elementtype;
    }
}
