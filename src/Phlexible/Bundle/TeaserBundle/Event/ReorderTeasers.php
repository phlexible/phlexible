<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

/**
 * Reorder teasers event
 *
 * @author Peter Fahssel <pfahsel@brainbits.net>
 */
class ReorderTeasersEvent extends \Symfony\Component\EventDispatcher\Event
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Teasers_Event::REORDER_TEASERS;
}