<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Field\Container;

use Phlexible\Bundle\ElementtypeBundle\Field\Field;

/**
 * Abstract container
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class AbstractContainer extends Field
{
    /**
     * {@inheritdoc}
     */
    public function isContainer()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isField()
    {
        return false;
    }
}
