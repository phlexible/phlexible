<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Field;

/**
 * Link field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LinkField extends AbstractField
{
    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return 'p-elementtype-field_link-icon';
    }

    /**
     * {@inheritdoc}
     */
    public function getDataType()
    {
        return 'json';
    }
}
