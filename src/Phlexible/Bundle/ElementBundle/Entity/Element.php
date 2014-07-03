<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Phlexible\Bundle\ElementtypeBundle\Entity\Elementtype;

/**
 * Element
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="element")
 */
class Element
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $eid;

    /**
     * @var string
     * @ORM\Column(name="unique_id", type="string", length=255, nullable=true, unique=true)
     */
    private $uniqueId;

    /**
     * @var Elementtype
     * @ORM\ManyToOne(targetEntity="Phlexible\Bundle\ElementtypeBundle\Entity\Elementtype")
     * @ORM\JoinColumn(name="elementtype_id", referencedColumnName="id")
     */
    private $elementtype;

    /**
     * @var string
     * @ORM\Column(name="master_language", type="string", length=2, options={"fixed"=true})
     */
    private $masterLanguage;

    /**
     * @var int
     * @ORM\Column(name="latest_version", type="integer")
     */
    private $latestVersion;

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
     * @return int
     */
    public function getEid()
    {
        return $this->eid;
    }

    /**
     * @param int $eid
     *
     * @return $this
     */
    public function setEid($eid)
    {
        $this->eid = $eid;

        return $this;
    }

    /**
     * @return string
     */
    public function getUniqueId()
    {
        return $this->uniqueId;
    }

    /**
     * @param string $uniqueId
     *
     * @return $this
     */
    public function setUniqueId($uniqueId)
    {
        $this->uniqueId = $uniqueId;

        return $this;
    }

    /**
     * @return int
     */
    public function getElementtypeId()
    {
        return $this->elementtypeId;
    }

    /**
     * @param int $elementtypeId
     *
     * @return $this
     */
    public function setElementtypeId($elementtypeId)
    {
        $this->elementtypeId = $elementtypeId;

        return $this;
    }

    /**
     * @return string
     */
    public function getMasterLanguage()
    {
        return $this->masterLanguage;
    }

    /**
     * @param string $masterLanguage
     *
     * @return $this
     */
    public function setMasterLanguage($masterLanguage)
    {
        $this->masterLanguage = $masterLanguage;

        return $this;
    }

    /**
     * @return int
     */
    public function getLatestVersion()
    {
        return $this->latestVersion;
    }

    /**
     * @param int $latestVersion
     *
     * @return $this
     */
    public function setLatestVersion($latestVersion)
    {
        $this->latestVersion = $latestVersion;

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
     * @param string $createUserId
     *
     * @return $this
     */
    public function setCreateUserId($createUserId)
    {
        $this->createUserId = $createUserId;

        return $this;
    }

    /**
     * Create a new Element Version
     *
     * @param string $elementTypeVersion (Optional) Element Type Version
     * @param string $comment            (Optional) Comment
     * @param bool   $minor              (Optional) Is this a minor update?
     * @param bool   $triggerLanguage    (Optional) Language that triggered the new version
     *
     * @return ElementVersion
     */
    public function xcreateVersion($elementTypeVersion = null, $comment = null, $minor = false, $triggerLanguage = null)
    {
        $db = MWF_Registry::getContainer()->dbPool->default;
        $dispatcher = Brainbits_Event_Dispatcher::getInstance();

        $eid = $this->getEID();

        // fetch new version
        $select = $db->select()
            ->from(
                $db->prefix . 'element_version',
                array(
                    'version',
                    new Zend_Db_Expr('version + 1 AS new_version'),
                    'element_type_version'
                )
            )
            ->where('eid = ?', $eid)
            ->order('version DESC')
            ->limit(1);

        $row = $db->fetchRow($select);

        if (!$row) // First version
        {
            $oldVersion = null;
            $newVersion = 1;

            if ($elementTypeVersion === null) {
                $elementTypeVersion = $this->getElementType()->getLatest()->getVersion();
            }
        } else // Increase version
        {
            $oldVersion = $row['version'];
            $newVersion = $row['new_version'];
            $elementTypeVersion = $row['element_type_version'];
        }

        // before version create event
        $event = new Makeweb_Elements_Event_BeforeCreateElementVersion($eid, $oldVersion, $newVersion);
        $dispatcher->dispatch($event);

        $elementTypeId = $this->elementTypeId;

        // insert new element version

        $insertData = array(
            'eid'                  => $eid,
            'version'              => $newVersion,
            'element_type_id'      => $elementTypeId,
            'element_type_version' => $elementTypeVersion,
            'format'               => Makeweb_Elements_Element_Version::CURRENT_FORMAT,
            'create_time'          => $db->fn->now(),
            'create_uid'           => MWF_Env::getUid(),
            'minor'                => $minor ? 1 : 0,
            'trigger_language'     => $triggerLanguage,
            'comment'              => $comment,
        );

        $db->insert($db->prefix . 'element_version', $insertData);

        // update latest_version column in element-table
        $updateData = array(
            'latest_version' => $newVersion
        );

        $db->update($db->prefix . 'element', $updateData, 'eid = ' . $eid);

        /* @var $latestElementVersions Makeweb_Elements_LatestElementVersions */
        $latestElementVersions = MWF_Registry::getContainer()->elementsLatestElementVersions;
        $latestElementVersions->setLatestElementVersion($eid, $newVersion);

        // retrieve new element version

        $elementVersion = $this->getVersion($newVersion);

        Makeweb_Elements_Element_History::insert(
            Makeweb_Elements_Element_History::ACTION_CREATE_VERSION,
            $eid,
            $elementVersion->getVersion(),
            null,
            $comment
        );

        // post message
        $message = new Makeweb_Elements_Message('Version ' . $newVersion . ' created for Element EID "' . $eid . '"');
        $message->post();

        // post event
        $event = new Makeweb_Elements_Event_CreateElementVersion($elementVersion, $oldVersion);
        $dispatcher->dispatch($event);

        return $elementVersion;
    }
}