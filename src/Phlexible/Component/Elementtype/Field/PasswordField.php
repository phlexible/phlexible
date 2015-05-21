<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Elementtype\Field;

/**
 * Password field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PasswordField extends TextField
{
    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return 'p-elementtype-field_password-icon';
    }
}
