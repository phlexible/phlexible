<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Element version.
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
     * 3 - trigger_language added.
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
     * @ORM\JoinColumn(name="eid", referencedColumnName="eid", onDelete="CASCADE")
     */
    private $element;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @var ElementSource
     * @ORM\ManyToOne(targetEntity="ElementSource")
     * @ORM\JoinColumn(name="element_source_id", referencedColumnName="id")
     */
    private $elementSource;

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
     * @var ArrayCollection|ElementVersionMappedField[]
     * @ORM\OneToMany(targetEntity="ElementVersionMappedField", mappedBy="elementVersion", indexBy="language", cascade={"persist","remove"}, orphanRemoval=true)
     */
    private $mappedFields;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->mappedFields = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

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
        return $this->getElementSource()->getId();
    }

    /**
     * @return ElementSource
     */
    public function getElementSource()
    {
        return $this->elementSource;
    }

    /**
     * @param ElementSource $elementSource
     *
     * @return $this
     */
    public function setElementSource(ElementSource $elementSource)
    {
        $this->elementSource = $elementSource;

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
     *
     * @return $this
     */
    public function setCreateUserId($createUserID)
    {
        $this->createUserId = $createUserID;

        return $this;
    }

    /**
     * @return string
     */
    public function getTriggerLanguage()
    {
        return $this->triggerLanguage;
    }

    /**
     * @param string $triggerLanguage
     *
     * @return $this
     */
    public function setTriggerLanguage($triggerLanguage)
    {
        $this->triggerLanguage = $triggerLanguage;

        return $this;
    }

    /**
     * @return ElementVersionMappedField|ArrayCollection
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
    public function setMappedFields($mappedFields = null)
    {
        $this->mappedFields = $mappedFields;

        return $this;
    }

    /**
     * @param ElementVersionMappedField $mappedField
     *
     * @return $this
     */
    public function addMappedField(ElementVersionMappedField $mappedField)
    {
        if (!$this->mappedFields->containsKey($mappedField->getLanguage())) {
            $this->mappedFields->set($mappedField->getLanguage(), $mappedField);
            $mappedField->setElementVersion($this);
        }

        return $this;
    }

    /**
     * Return backend title.
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
     * Return page title.
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
     * Return navigation title.
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
     * Return custom date.
     *
     * @param string $language
     * @param bool   $fallbackLanguage
     *
     * @return \DateTime
     */
    public function getCustomDate($language, $fallbackLanguage = null)
    {
        $date = $this->getMappedField('date', $language, $fallbackLanguage);

        return $date;
    }

    /**
     * Return mapped field.
     *
     * @param string $field
     * @param string $language
     * @param string $fallbackLanguage
     *
     * @return string
     */
    public function getMappedField($field, $language, $fallbackLanguage = null)
    {
        if ($this->mappedFields->containsKey($language)) {
            $mappedField = $this->mappedFields->get($language);
        } elseif ($this->mappedFields->containsKey($fallbackLanguage)) {
            $mappedField = $this->mappedFields->get($fallbackLanguage);
        } else {
            foreach ($this->mappedFields as $testMappedField) {
                if ($testMappedField->getLanguage() === $language) {
                    $mappedField = $testMappedField;
                    break;
                }
            }
            if (!isset($mappedField)) {
                foreach ($this->mappedFields as $testMappedField) {
                    if ($testMappedField->getLanguage() === $fallbackLanguage) {
                        $mappedField = $testMappedField;
                        break;
                    }
                }
            }
            if (!isset($mappedField)) {
                return null;
            }
        }

        if ($field === 'page') {
            if ($mappedField->getPage()) {
                return $mappedField->getPage();
            } else {
                $field = 'backend';
            }
        }

        if ($field === 'navigation') {
            if ($mappedField->getNavigation()) {
                return $mappedField->getNavigation();
            } else {
                $field = 'backend';
            }
        }

        if ($field === 'backend') {
            return $mappedField->getBackend();
        }

        if ($field === 'date' && $mappedField->getDate()) {
            return $mappedField->getDate();
        }

        if ($field === 'forward' && $mappedField->getForward()) {
            return json_decode($mappedField->getForward(), true);
        }

        if ($field === 'custom1' && $mappedField->getCustom1()) {
            return $mappedField->getCustom1();
        } elseif ($field === 'custom2' && $mappedField->getCustom2()) {
            return $mappedField->getCustom2();
        } elseif ($field === 'custom3' && $mappedField->getCustom3()) {
            return $mappedField->getCustom3();
        } elseif ($field === 'custom4' && $mappedField->getCustom4()) {
            return $mappedField->getCustom4();
        } elseif ($field === 'custom5' && $mappedField->getCustom5()) {
            return $mappedField->getCustom5();
        }

        return null;
    }
}
