<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Field;

/**
 * Number field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NumberField extends AbstractField
{
    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return 'p-elementtype-field_number-icon';
    }

    /**
     * {@inheritdoc}
     */
    public function getDataType()
    {
        return 'float';
    }

}
