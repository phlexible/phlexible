<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle;

use Phlexible\Bundle\MessageBundle\Entity\Message;

/**
 * Data source message
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DataSourceMessage extends Message
{
    /**
     * {@inheritdoc}
     */
    public static function getDefaultChannel()
    {
        return 'datasource';
    }

    /**
     * {@inheritdoc}
     */
    public static function getDefaultRole()
    {
        return 'ROLE_DATA_SOURCES';
    }
}
