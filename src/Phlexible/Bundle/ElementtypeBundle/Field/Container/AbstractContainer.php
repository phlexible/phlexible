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
    protected $isContainer = true;
    protected $isField = false;
    protected $hasContent = false;

    /**
     * Transform item values
     *
     * @param array $item
     * @param array $media
     * @param array $options
     *
     * @return array
     */
    protected function _transform(array $item, array $media, array $options)
    {
        return $item;
    }
}
