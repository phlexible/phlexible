<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Change;

/**
 * Elementtype change
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ChangeInterface
{
    /**
     * @return string
     */
    public function getReason();
}
