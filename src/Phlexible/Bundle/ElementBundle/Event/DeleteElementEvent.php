<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Event;

use Phlexible\Bundle\ElementBundle\Entity\Element;

/**
 * Delete element event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DeleteElementEvent extends ElementEvent
{
    /**
     * @var int
     */
    private $eid;

    /**
     * @param Element $element
     * @param int     $eid
     */
    public function __construct(Element $element, $eid)
    {
        parent::__construct($element);

        $this->eid = $eid;
    }

    /**
     * @return int
     */
    public function getEid()
    {
        return $this->eid;
    }
}
