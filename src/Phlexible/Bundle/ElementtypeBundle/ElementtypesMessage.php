<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle;

use Phlexible\Bundle\MessageBundle\Entity\Message;

/**
 * Elementtypes message
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypesMessage extends Message
{
    /**
     * {@inheritdoc}
     */
    public static function getDefaultChannel()
    {
        return 'elementtype';
    }

    /**
     * {@inheritdoc}
     */
    public static function getDefaultRole()
    {
        return 'ROLE_ELEMENTTYPES';
    }
}