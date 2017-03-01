<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementtypeBundle\Field;

/**
 * Password field.
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
