<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\FrontendMediaBundle\Field;

use Phlexible\Bundle\ElementtypeBundle\Field\AbstractField;

/**
 * Folder field.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FolderField extends AbstractField
{
    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return 'p-frontendmedia-field_folder-icon';
    }

    /**
     * {@inheritdoc}
     */
    public function getDataType()
    {
        return 'string';
    }
}
