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
 * Abstract field.
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
     * From submitted value to object.
     *
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
        } elseif ($type === 'list') {
            $value = explode(',', $value);
        } elseif ($type === 'boolean') {
            $value = $value ? '1' : '0';
        } elseif ($type === 'integer') {
            $value = (int) $value;
        } elseif ($type === 'float') {
            $value = (float) $value;
        }

        return $value;
    }

    /**
     * From object to database.
     *
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
        } elseif ($this->getDataType() === 'list') {
            return implode(',', $value);
        }

        return trim($value);
    }

    /**
     * From database to object.
     *
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
        } elseif ($type === 'list') {
            $value = explode(',', $value);
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
