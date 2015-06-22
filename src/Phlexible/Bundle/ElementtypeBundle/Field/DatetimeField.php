<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Field;

/**
 * Datetime field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DatetimeField extends AbstractField
{
    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return 'p-elementtype-field_datetime-icon';
    }

    /**
     * {@inheritdoc}
     */
    public function getDataType()
    {
        return 'datetime';
    }

}