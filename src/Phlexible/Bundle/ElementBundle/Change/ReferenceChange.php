<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Change;

/**
 * Elementtype reference change
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ReferenceChange extends UpdateChange
{
    /**
     * {@inheritdoc}
     */
    public function getReason()
    {
        return 'Reference elementtype updated';
    }
}
