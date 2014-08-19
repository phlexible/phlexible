<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Element version
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity(repositoryClass="Phlexible\Bundle\ElementBundle\Entity\Repository\ElementVersionRepository")
 * @ORM\Table(name="element_version")
 */
class ElementVersion
{
    /**
     * Current format:
     * 3 - trigger_language added
     *
     * Prior formats:
     * 2 - element data (language) / data_id changes
     * 1 - initial version
     *
     * @var int
     */
    const CURRENT_FORMAT = 3;

    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Element
     * @ORM\ManyToOne(targetEntity="Element")
     * @ORM\JoinColumn(name="eid", referencedColumnName="eid")
     */
    private $element;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @var int
     * @ORM\Column(name="elementtype_version", type="integer")
     */
    private $elementtypeVersion;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     * @ORM\Column(name="create_user_id", type="string", length=36, options={"fixed"=true})
     */
    private $createUserId;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $minor = false;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $format = false;

    /**
     * @var string
     * @ORM\Column(name="trigger_language", type="string", length=2, nullable=true, options={"fixed"=true})
     */
    private $triggerLanguage;

    /**
     * @var array
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $mappedFields = array();

    /**
     * @return Element
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @param Element $element
     *
     * @return $this
     */
    public function setElement(Element $element)
    {
        $this->element = $element;

        return $this;
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param int $version
     *
     * @return $this
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return int
     */
    public function getElementtypeVersion()
    {
        return $this->elementtypeVersion;
    }

    /**
     * @param int $elementtypeVersion
     *
     * @return $this
     */
    public function setElementtypeVersion($elementtypeVersion)
    {
        $this->elementtypeVersion = $elementtypeVersion;

        return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     *
     * @return $this
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getCreateUserId()
    {
        return $this->createUserId;
    }

    /**
     * @param string $createUserID
     */
    public function setCreateUserId($createUserID)
    {
        $this->createUserId = $createUserID;
    }

    /**
     * @return array
     */
    public function getMappedFields()
    {
        return $this->mappedFields;
    }

    /**
     * @param array $mappedFields
     *
     * @return $this
     */
    public function setMappedFields(array $mappedFields = null)
    {
        $this->mappedFields = $mappedFields;

        return $this;
    }

    /**
     * Return backend title
     *
     * @param string $language
     * @param bool   $fallbackLanguage
     *
     * @return string
     */
    public function getBackendTitle($language, $fallbackLanguage = null)
    {
        return $this->getMappedField('backend', $language, $fallbackLanguage);
    }

    /**
     * Return page title
     *
     * @param string $language
     * @param bool   $fallbackLanguage
     *
     * @return string
     */
    public function getPageTitle($language, $fallbackLanguage = null)
    {
        return $this->getMappedField('page', $language, $fallbackLanguage);
    }

    /**
     * Return navigation title
     *
     * @param string $language
     * @param bool   $fallbackLanguage
     *
     * @return string
     */
    public function getNavigationTitle($language, $fallbackLanguage = null)
    {
        return $this->getMappedField('navigation', $language, $fallbackLanguage);
    }

    /**
     * Return custom date
     *
     * @param string $language
     * @param bool   $fallbackLanguage
     *
     * @return \DateTime
     */
    public function getCustomDate($language, $fallbackLanguage = null)
    {
        $date = $this->getMappedField('date', $language, $fallbackLanguage);

        $date = new \DateTime($date);

        return $date;
    }

    /**
     * Return mapped field
     *
     * @param string $field
     * @param string $language
     * @param string $fallbackLanguage
     *
     * @return string
     */
    public function getMappedField($field, $language, $fallbackLanguage = null)
    {
        if (in_array($field, array('page', 'navigation'))
            && !isset($this->mappedFields[$language][$field])
            && !isset($this->mappedFields[$fallbackLanguage][$field])
        ) {
            $field = 'backend';
        }

        if (isset($this->mappedFields[$language][$field])) {
            return $this->mappedFields[$language][$field];
        }

        if (isset($this->mappedFields[$fallbackLanguage][$field])) {
            return $this->mappedFields[$fallbackLanguage][$field];
        }

        return null;
    }

    /**
     * Return icon url of the current element
     *
     * @param array $additionalParams
     *
     * @return string
     */
    public function getIconUrl(array $additionalParams = array())
    {
        // TODO: repair!
        return '/elements/asset/';
        $icon = '/elements/asset/' . $this->getElementTypeVersionObj()->getIcon();

        //        $uid = MWF_Env::getUid();
        //        $service = $this->getContainer()->get('locks.service');
        //        $lockIdentifier = new Makeweb_Elements_Element_Identifier($this->_eid);
        //
        //        if ($service->isLockedByUser($lockIdentifier, $uid))
        //        {
        //            $icon .= '/lock/me';
        //        }
        //        elseif ($service->isLocked($lockIdentifier))
        //        {
        //            $icon .= '/lock/other';
        //        }

        foreach ($additionalParams as $key => $value) {
            if ($value) {
                $icon .= '/' . $key . '/' . $value;
            }
        }

        return $icon;
    }

    /**
     * Return data for this element
     *
     * @param string $language
     *
     * @return ElementVersionData
     */
    public function xgetData($language, $mode, $diffFromVersion = null, $diffToVersion = null, $diffLanguage = null)
    {
        if (empty($this->data[$language][$mode])) {
            $identifier = new Makeweb_Elements_Element_Version_Data_Identifier($this->_eid, $language, $this->version, $mode);

            $etUniqueId = $this->getElementTypeVersionObj()->getElementtype()->getUniqueId();
            if (($data && $data->getCacheVersion() !== Makeweb_Elements_Element_Version_Data::CACHE_VERSION) ||
                defined('DISABLE_ELEMENT_VERSION_DATA_CACHE') ||
                $mode === Makeweb_Elements_Element_Version_Data::MODE_DIFF ||
                !$data ||
                $etUniqueId == 'businesslogic' ||
                $etUniqueId == 'servicetools'
            ) {
                $data = new Makeweb_Elements_Element_Version_Data(
                    $this->_eid,
                    $language,
                    $this->version,
                    $this->_elementTypeID,
                    $this->_elementTypeVersion,
                    $mode
                );

                if ($mode === Makeweb_Elements_Element_Version_Data::MODE_DIFF) {
                    $data->setDiffFromVersion($diffFromVersion);
                    $data->setDiffToVersion($diffToVersion);
                    $data->setDiffLanguage($diffLanguage);
                }

                $data->getTree();
            }

            $this->data[$language][$mode] = $data;
        }

        return $this->data[$language][$mode];
    }

    /**
     * Create a new Element Version
     *
     * @param int $targetEid             Target EID
     * @param int $newElementTypeVersion New Element Type Version
     *
     * @return Makeweb_Elements_Element_Version
     */
    public function copy(
        $targetEid = null,
        $newElementTypeVersion = null)
    {
        $db = MWF_Registry::getContainer()->dbPool->default;
        $dispatcher = Brainbits_Event_Dispatcher::getInstance();

        $sourceEid = $this->getEID();

        if ($targetEid === null) {
            $targetEid = $sourceEid;
        } else {
            $select = $db->select()
                ->from($db->prefix . 'element', array('element_type_id', 'masterlanguage'))
                ->where('eid = ?', $targetEid);

            $result = $db->fetchRow($select);

            $targetElementTypeId = $result['element_type_id'];

            if ($this->_elementTypeID != $targetElementTypeId) {
                throw new Makeweb_Elements_Element_Exception('Element Type Ids don\'t match.');
            }
        }

        // fetch source element version

        $sourceVersion = $this->version;
        $sourceElementTypeVersion = $this->getElementTypeVersionObj()->getVersion();

        if ($newElementTypeVersion === null) {
            $newElementTypeVersion = $sourceElementTypeVersion;
        }

        // fetch target element version

        $select = $db->select()
            ->from(
                $db->prefix . 'element_version',
                array(
                    'format',
                    'new_version' => new Zend_Db_Expr('version + 1')
                )
            )
            ->where('eid = ?', $targetEid)
            ->order('version DESC')
            ->limit(1);

        $targetVersionRow = $db->fetchRow($select);

        if ($targetVersionRow) {
            $targetVersion = $targetVersionRow['new_version'];
            $targetFormat = $targetVersionRow['format'];
        } else {
            $targetVersion = 1;
            $targetFormat = self::CURRENT_FORMAT;
        }

        // before version create event
        $event = new Makeweb_Elements_Event_BeforeCreateElementVersion($targetEid, $targetVersion);
        $dispatcher->dispatch($event);

        // insert new element version

        $insertData = array(
            'eid'                  => $targetEid,
            'version'              => $targetVersion,
            'element_type_id'      => $this->_elementTypeID,
            'element_type_version' => $newElementTypeVersion,
            'create_time'          => $db->fn->now(),
            'create_uid'           => MWF_Env::getUid(),
            'format'               => $targetFormat,
        );

        $db->insert($db->prefix . 'element_version', $insertData);

        // update latest_version information in element

        $updateData = array(
            'latest_version' => $targetVersion
        );

        $db->update($db->prefix . 'element', $updateData, array('eid = ?' => $targetEid));

        /* @var $latestElementVersions Makeweb_Elements_LatestElementVersions */
        $latestElementVersions = MWF_Registry::getContainer()->elementsLatestElementVersions;
        $latestElementVersions->setLatestElementVersion($targetEid, $targetVersion);

        // fetch variant field information

        $elementTypeVersionManager = Makeweb_Elementtypes_Elementtype_Version_Manager::getInstance();
        $elementTypeVersion = $elementTypeVersionManager->get(
            $this->_elementTypeID,
            $newElementTypeVersion
        );

        // copy element version data

        $dataManager = Makeweb_Elements_Element_Version_Data_Manager::getInstance();
        $dataManager->copyData(
            $sourceEid,
            $sourceVersion,
            $targetEid,
            $targetVersion
        );

        // copy metasets

        $setId = $elementTypeVersion->getMetaSetId();
        if ($setId) {
            $select = $db->select()
                ->from($db->prefix . 'element_version_metaset_items', array('key', 'language', 'value'))
                ->where('set_id = ?', $setId)
                ->where('eid = ?', $sourceEid)
                ->where('version = ?', $sourceVersion);

            $metaValues = $db->fetchAll($select);

            foreach ($metaValues as $row) {
                $insertData = array(
                    'set_id'   => $setId,
                    'eid'      => $targetEid,
                    'version'  => $targetVersion,
                    'language' => $row['language'],
                    'key'      => $row['key'],
                    'value'    => $row['value'],
                );

                $db->insert($db->prefix . 'element_version_metaset_items', $insertData);
            }
        }

        // retrieve new element version
        $elementVersionManager = Makeweb_Elements_Element_Version_Manager::getInstance();
        $elementVersion = $elementVersionManager->get($targetEid, $targetVersion);

        // clean cache
        $identifier = new Makeweb_Elements_Element_Identifier($targetEid);

        // post message
        $message = new Makeweb_Elements_Message('EID ' . $sourceEid . ' Version ' . $sourceVersion . ' copied to EID ' . $targetEid . ' Version ' . $targetVersion);
        $message->post();

        // post event
        $event = new Makeweb_Elements_Event_CreateElementVersion($elementVersion);
        $dispatcher->dispatch($event);

        Makeweb_Elements_Element_History::insert(
            Makeweb_Elements_Element_History::ACTION_CREATE_VERSION,
            $targetEid,
            $targetVersion,
            null,
            'Automatically created by new elementtype version.'
        );

        return $elementVersion;
    }
}