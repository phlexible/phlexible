<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Field;

/**
 * Radio field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RadioField extends AbstractField
{
    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return 'p-elementtype-field_radio-icon';
    }

    /**
     * {@inheritdoc}
     */
    public function getDataType()
    {
        return 'boolean';
    }
}
