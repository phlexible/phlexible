<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Field;

/**
 * Table field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TableField extends AbstractField
{
    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return 'p-elementtype-field_table-icon';
    }

    /**
     * {@inheritdoc}
     */
    public function getDataType()
    {
        return 'array';
    }
}
