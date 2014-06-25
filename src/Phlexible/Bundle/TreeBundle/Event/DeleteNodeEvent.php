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
 * Delete node event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DeleteNodeEvent extends BeforeCreateNodeEvent
{
    /**
     * @var string
     */
    protected $eventName = Events::DELETE_NODE;
}