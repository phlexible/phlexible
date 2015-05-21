<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Elementtype\Field;

/**
 * Date field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DateField extends AbstractField
{
    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return 'p-elementtype-field_date-icon';
    }

    /**
     * {@inheritdoc}
     */
    public function getDataType()
    {
        return 'date';
    }
}
