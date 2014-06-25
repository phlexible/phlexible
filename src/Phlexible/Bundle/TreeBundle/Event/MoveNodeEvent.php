<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Event;

use Phlexible\Bundle\TreeBundle\Events;

/**
 * Move node event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MoveNodeEvent extends AbstractNodeEvent
{
    /**
     * @var string
     */
    protected $eventName = Events::MOVE_NODE;
}