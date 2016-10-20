<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TeaserBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Before show inherited teaser event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BeforeHideInheritedTeaserEvent extends Event
{
    /**
     * @var int
     */
    private $treeId;

    /**
     * @var int
     */
    private $eid;

    /**
     * @var int
     */
    private $teaserId;

    /**
     * @var int
     */
    private $layoutareaId;

    /**
     * @param int $treeId
     * @param int $eid
     * @param int $teaserId
     * @param int $layoutareaId
     */
    public function __construct($treeId, $eid, $teaserId, $layoutareaId)
    {
        $this->treeId = $treeId;
        $this->eid = $eid;
        $this->teaserId = $teaserId;
        $this->layoutareaId = $layoutareaId;
    }

    /**
     * @return int
     */
    public function getTreeId()
    {
        return $this->treeId;
    }

    /**
     * @return int
     */
    public function getEid()
    {
        return $this->eid;
    }

    /**
     * @return int
     */
    public function getTeaserId()
    {
        return $this->teaserId;
    }

    /**
     * @return int
     */
    public function getLayoutareaId()
    {
        return $this->layoutareaId;
    }
}
