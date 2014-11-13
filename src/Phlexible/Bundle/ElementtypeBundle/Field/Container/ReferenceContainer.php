<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Field\Container;

/**
 * Reference container
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ReferenceContainer extends AbstractContainer
{
    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return 'p-elementtype-container_reference-icon';
    }
}
