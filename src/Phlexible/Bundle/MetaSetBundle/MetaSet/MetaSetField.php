<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\MetaSet;

/**
 * Meta set field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaSetField
{
    /*

                'key'          => $key,
                'type'         => $type,
                'options'      => $options,
                'required'     => $required,
                'readonly'     => $readonly,
                'synchronized' => $synchronized,
     */

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $options;

    /**
     * @var bool
     */
    private $required;

    /**
     * @var bool
     */
    private $readonly;

    /**
     * @var bool
     */
    private $synchronized;

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param bool $required
     *
     * @return $this
     */
    public function setRequired($required = true)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * @return array
     */
    public function isReadonly()
    {
        return $this->readonly;
    }

    /**
     * @param bool $readonly
     *
     * @return $this
     */
    public function setReadonly($readonly = true)
    {
        $this->readonly = $readonly;

        return $this;
    }

    /**
     * @return array
     */
    public function isSynchronized()
    {
        return $this->synchronized;
    }

    /**
     * @param bool $synchronized
     *
     * @return $this
     */
    public function setSynchronized($synchronized = true)
    {
        $this->synchronized = $synchronized;

        return $this;
    }
}