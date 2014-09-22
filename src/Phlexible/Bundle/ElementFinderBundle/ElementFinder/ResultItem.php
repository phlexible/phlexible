<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder;

/**
 * Result item
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ResultItem
{
    /**
     * @var int
     */
    private $treeId;

    /**
     * @var int
     */
    private $eid;

    /**
     * @var int
     */
    private $version;

    /**
     * @var string
     */
    private $language;

    /**
     * @var int
     */
    private $elementtypeId;

    /**
     * @var bool
     */
    private $inNavigation;

    /**
     * @var bool
     */
    private $isRestricted;

    /**
     * @var \DateTime
     */
    private $publishedAt;

    /**
     * @var \DateTime
     */
    private $customDate;

    /**
     * @param int       $treeId
     * @param int       $eid
     * @param int       $version
     * @param string    $language
     * @param int       $elementtypeId
     * @param bool      $inNavigation
     * @param bool      $isRestricted
     * @param \DateTime $publishedAt
     * @param \DateTime $customDate
     */
    public function __construct($treeId, $eid, $version, $language, $elementtypeId, $inNavigation, $isRestricted, $publishedAt, $customDate)
    {
        $this->treeId = $treeId;
        $this->eid = $eid;
        $this->version = $version;
        $this->language = $language;
        $this->elementtypeId = $elementtypeId;
        $this->inNavigation = $inNavigation;
        $this->isRestricted = $isRestricted;
        $this->publishedAt = $publishedAt;
        $this->customDate = $customDate;
    }

    /**
     * @return \DateTime
     */
    public function getCustomDate()
    {
        return $this->customDate;
    }

    /**
     * @return int
     */
    public function getEid()
    {
        return $this->eid;
    }

    /**
     * @return int
     */
    public function getElementtypeId()
    {
        return $this->elementtypeId;
    }

    /**
     * @return boolean
     */
    public function isInNavigation()
    {
        return $this->inNavigation;
    }

    /**
     * @return boolean
     */
    public function isIsRestricted()
    {
        return $this->isRestricted;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return \DateTime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * @return int
     */
    public function getTreeId()
    {
        return $this->treeId;
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

}