<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ContentElement;

use Phlexible\Bundle\ElementBundle\ElementStructure\ElementStructure;

/**
 * Content element
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ContentElement
{
    /**
     * @var int
     */
    private $eid;

    /**
     * @var string
     */
    private $uniqueId;

    /**
     * @var int
     */
    private $elementtypeId;

    /**
     * @var string
     */
    private $elementtypeUniqueId;

    /**
     * @var string
     */
    private $elementtypeType;

    /**
     * @var string
     */
    private $language;

    /**
     * @var int
     */
    private $version;

    /**
     * @var array
     */
    private $mappedFields = array();

    /**
     * @var ElementStructure
     */
    private $structure;

    /**
     * @param int              $eid
     * @param string           $uniqueId
     * @param int              $elementtypeId
     * @param string           $elementtypeUniqueId
     * @param string           $elementtypeType
     * @param int              $version
     * @param string           $language
     * @param array            $mappedFields
     * @param ElementStructure $structure
     */
    public function __construct($eid,
                                $uniqueId,
                                $elementtypeId,
                                $elementtypeUniqueId,
                                $elementtypeType,
                                $version,
                                $language,
                                array $mappedFields,
                                ElementStructure $structure)
    {
        $this->eid = (integer) $eid;
        $this->uniqueId = $uniqueId ?: null;
        $this->elementtypeId = $elementtypeId;
        $this->elementtypeUniqueId = $elementtypeUniqueId;
        $this->elementtypeType = $elementtypeType;
        $this->version = (integer) $version;
        $this->language = $language;
        $this->mappedFields = $mappedFields;
        $this->structure = $structure;
    }

    /**
     * @return int
     */
    public function getEid()
    {
        return $this->eid;
    }

    /**
     * @return string
     */
    public function getUniqueId()
    {
        return $this->uniqueId;
    }

    /**
     * @return string
     */
    public function getElementtypeId()
    {
        return $this->elementtypeId;
    }

    /**
     * @return string
     */
    public function getElementtypeUniqueId()
    {
        return $this->elementtypeUniqueId;
    }

    /**
     * @return string
     */
    public function getElementtypeType()
    {
        return $this->elementtypeType;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return array
     */
    public function getMappedFields()
    {
        return $this->mappedFields;
    }

    /**
     * @return ElementStructure
     */
    public function getStructure()
    {
        return $this->structure;
    }
}