<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\MetaSet;

/**
 * Meta set interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface MetaSetInterface
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * @return integer
     */
    public function getRevision();

    /**
     * @param integer $revision
     *
     * @return $this
     */
    public function setRevision($revision);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title);

    /**
     * @return MetaSetField[]
     */
    public function getFields();

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasField($key);

    /**
     * @param MetaSetField[] $fields
     *
     * @return $this
     */
    public function setFields(array $fields);

    /**
     * @param MetaSetField $field
     *
     * @return $this
     */
    public function addField(MetaSetField $field);
}