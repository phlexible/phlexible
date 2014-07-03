<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Slot;

/**
 * Slots interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface SlotInterface
{
    /**
     * @return string
     */
    public function getKey();

    /**
     * @return array
     */
    public function getData();
}