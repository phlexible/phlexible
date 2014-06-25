<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Field;

/**
 * Abstract field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class AbstractField extends Field
{
    protected $isField     = true;
    protected $isContainer = true;
    protected $hasContent  = true;
    protected $hasOptions  = false;

    /**
     * @var string
     */
    protected $dataType = 'string';

    /**
     * @return string
     */
    public function getDataType()
    {
        return $this->dataType;
    }

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
