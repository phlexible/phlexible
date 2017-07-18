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

use Phlexible\Bundle\ElementBundle\Entity\ElementLink;

/**
 * Delete element link event.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DeleteElementLinkEvent extends ElementLinkEvent
{
    /**
     * @var int
     */
    private $id;

    /**
     * @param ElementLink $elementLink
     * @param int         $id
     */
    public function __construct(ElementLink $elementLink, $id)
    {
        parent::__construct($elementLink);

        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
