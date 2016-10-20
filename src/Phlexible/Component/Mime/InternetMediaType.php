<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Mime;

/**
 * Internet media type
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class InternetMediaType
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $subtype;

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @param string $type
     * @param string $subtype
     * @param array  $parameters
     */
    public function __construct($type = null, $subtype = null, array $parameters = [])
    {
        $this->type = $type;
        $this->subtype = $subtype;
        $this->parameters = $parameters;
    }

    /**
     * Set type
     *
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
     * Return type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Is a type set?
     *
     * @return boolean
     */
    public function hasType()
    {
        return strlen($this->type) > 0;
    }

    /**
     * Set subtype
     *
     * @param string $subtype
     *
     * @return $this
     */
    public function setSubtype($subtype)
    {
        $this->subtype = $subtype;

        return $this;
    }

    /**
     * Return subtype
     *
     * @return string
     */
    public function getSubtype()
    {
        return $this->subtype;
    }

    /**
     * Is a subtype set?
     *
     * @return boolean
     */
    public function hasSubtype()
    {
        return strlen($this->subtype) > 0;
    }

    /**
     * Set parameters
     *
     * @param array $paramaters
     *
     * @return $this
     */
    public function setParameters(array $paramaters)
    {
        $this->parameters = $paramaters;

        return $this;
    }

    /**
     * Return parameters
     *
     * @return string
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Are parameters set?
     *
     * @return boolean
     */
    public function hasParameters()
    {
        return count($this->parameters) > 0;
    }

    /**
     * Set parameter
     *
     * @param string $attribute
     * @param string $value
     *
     * @return $this
     */
    public function setParameter($attribute, $value)
    {
        $this->parameters[$attribute] = $value;

        return $this;
    }

    /**
     * Return parameter
     *
     * @param string $attribute
     *
     * @return string
     */
    public function getParameter($attribute)
    {
        return $this->parameters[$attribute];
    }

    /**
     * Return string representation
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Return string representation
     *
     * @return string
     */
    public function toString()
    {
        $string = $this->toStringWithoutParameters();

        if (!$this->hasParameters()) {
            return $string;
        }

        $parameters = '';
        foreach ($this->parameters as $attribute => $value) {
            $parameters .= ' ' . $attribute . '=' . $value;
        }

        $string .= ';' . $parameters;

        return $string;
    }

    /**
     * Return string representation without parameters
     *
     * @return string
     */
    public function toStringWithoutParameters()
    {
        $string = '';

        if (!$this->hasType()) {
            return $string;
        }

        if (!$this->hasSubtype()) {
            return $string;
        }

        return $this->getType() . '/' . $this->getSubtype();
    }
}
