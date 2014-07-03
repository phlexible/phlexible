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
 * Before delete node event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BeforeDeleteNodeEvent extends AbstractNodeEvent
{
    /**
     * @var string
     */
    protected $eventName = Events::BEFORE_DELETE_NODE;
}