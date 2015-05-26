<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Field;

/**
 * Textarea field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TextareaField extends TextField
{
    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return 'p-elementtype-field_textarea-icon';
    }
}
