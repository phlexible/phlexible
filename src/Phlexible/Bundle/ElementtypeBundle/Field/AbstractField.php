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
    /**
     * {@inheritdoc}
     */
    public function isContainer()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isField()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getDataType()
    {
        return 'string';
    }

    /**
     * @param string $value
     *
     * @return mixed
     */
    public function fromRaw($value)
    {
        $type = $this->getDataType();

        if ($type === 'json') {
            $value = json_decode($value, true);
        } elseif ($type === 'array') {
            $value = (array) $value;
        } elseif ($type === 'boolean') {
            $value = (bool) $value;
        } elseif ($type === 'integer') {
            $value = (int) $value;
        } elseif ($type === 'float') {
            $value = (float) $value;
        }

        return $value;
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    public function serialize($value)
    {
        if ($this->getDataType() === 'json') {
            return json_encode($value);
        } elseif ($this->getDataType() === 'array') {
            return json_encode($value);
        }

        return trim($value);
    }

    /**
     * @param string $value
     *
     * @return mixed
     */
    public function unserialize($value)
    {
        $type = $this->getDataType();

        if ($type === 'json') {
            $value = json_decode($value, true);
        } elseif ($type === 'array') {
            $value = json_decode($value, true);
        } elseif ($type === 'boolean') {
            $value = (bool) $value;
        } elseif ($type === 'integer') {
            $value = (int) $value;
        } elseif ($type === 'float') {
            $value = (float) $value;
        }

        return $value;
    }
}
