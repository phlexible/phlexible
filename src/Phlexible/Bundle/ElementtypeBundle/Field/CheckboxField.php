<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Field;

/**
 * Checkbox field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CheckboxField extends AbstractField
{
    protected $hasOptions = true;

    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return 'p-elementtype-field_checkbox-icon';
    }

    /**
     * {@inheritdoc}
     */
    public function getDataType()
    {
        return 'boolean';
    }
}
