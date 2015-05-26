<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Field;

use Phlexible\Bundle\ElementtypeBundle\Exception\FieldNotAvailableException;

/**
 * Field registry
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FieldRegistry
{
    /**
     * @var array
     */
    private $fields;

    /**
     * @param array $fields
     */
    public function __construct(array $fields = [])
    {
        $this->setFields($fields);
    }

    /**
     * @param array $fields
     *
     * @return $this
     */
    public function setFields(array $fields)
    {
        foreach ($fields as $key => $field) {
            $this->setField($key, $field);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param string $key
     * @param string $field
     *
     * @return $this
     */
    public function setField($key, $field)
    {
        $this->fields[$key] = $field;

        return $this;
    }

    /**
     * Returns all titles
     *
     * @param bool $onlyUsable
     *
     * @return array
     */
    public function xgetFieldTypes($onlyUsable = false)
    {
        $fieldTypes = array_keys($this->_getFields());

        if ($onlyUsable) {
            unset($fieldTypes['root']);
            unset($fieldTypes['referenceroot']);
            unset($fieldTypes['reference']);
        }

        sort($fieldTypes);

        return $fieldTypes;
    }

    /**
     * Get a field
     *
     * @param string $key
     *
     * @throws FieldNotAvailableException
     * @return Field
     */
    public function getField($key)
    {
        if (!$this->hasField($key)) {
            throw new FieldNotAvailableException("Content field $key not available. If it is provided by a component, please check if this component is installed.");
        }

        $field = $this->fields[$key];

        return new $field();
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasField($key)
    {
        return isset($this->fields[$key]);
    }
}
