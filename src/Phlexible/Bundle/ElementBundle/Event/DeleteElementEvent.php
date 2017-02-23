<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Event;

use Phlexible\Bundle\ElementBundle\Entity\Element;

/**
 * Delete element event.
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
