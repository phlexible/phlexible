<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FormBundle\Field;

use Phlexible\Bundle\ElementtypeBundle\Field\AbstractField;

/**
 * Form field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FormField extends AbstractField
{
    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return  'p-form-form-icon';
    }

    /**
     * {@inheritdoc}
     */
    public function getDataType()
    {
        return 'string';
    }
}
