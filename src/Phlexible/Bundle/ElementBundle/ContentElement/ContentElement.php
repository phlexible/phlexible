<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ContentElement;

use Phlexible\Bundle\ElementBundle\Entity\ElementVersionMappedField;
use Phlexible\Bundle\ElementBundle\Model\ElementStructure;

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
    private $elementtypeTemplate;

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
    private $mappedField = array();

    /**
     * @var ElementStructure
     */
    private $structure;

    /**
     * @param int                       $eid
     * @param string                    $uniqueId
     * @param int                       $elementtypeId
     * @param string                    $elementtypeUniqueId
     * @param string                    $elementtypeType
     * @param string                    $elementtypeTemplate
     * @param int                       $version
     * @param string                    $language
     * @param ElementVersionMappedField $mappedField
     * @param ElementStructure          $structure
     */
    public function __construct(
        $eid,
        $uniqueId,
        $elementtypeId,
        $elementtypeUniqueId,
        $elementtypeType,
        $elementtypeTemplate,
        $version,
        $language,
        ElementVersionMappedField $mappedField = null,
        ElementStructure $structure
    ) {
        $this->eid = (int) $eid;
        $this->uniqueId = $uniqueId ? : null;
        $this->elementtypeId = $elementtypeId;
        $this->elementtypeUniqueId = $elementtypeUniqueId;
        $this->elementtypeType = $elementtypeType;
        $this->elementtypeTemplate = $elementtypeTemplate;
        $this->version = (int) $version;
        $this->language = $language;
        $this->mappedField = $mappedField;
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
    public function getElementtypeTemplate()
    {
        return $this->elementtypeTemplate;
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
    public function getMappedField()
    {
        return $this->mappedField;
    }

    /**
     * @return ElementStructure
     */
    public function getStructure()
    {
        return $this->structure;
    }
}