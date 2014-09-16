<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendMediaBundle\Field;

use Phlexible\Bundle\ElementtypeBundle\Field\AbstractField;

/**
 * Abstract media field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FileField extends AbstractField
{
    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return 'p-frontendmedia-field_file-icon';
    }

    /**
     * {@inheritdoc}
     */
    public function getDataType()
    {
        return 'string';
    }
}