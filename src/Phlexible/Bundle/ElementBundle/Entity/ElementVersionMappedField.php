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
 * Element version mapped field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="element_version_mapped_field")
 */
class ElementVersionMappedField
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var ElementVersion
     * @ORM\ManyToOne(targetEntity="ElementVersion", inversedBy="mappedFields")
     * @ORM\JoinColumn(name="element_version_id", referencedColumnName="id")
     */
    private $elementVersion;

    /**
     * @var string
     * @ORM\Column(type="string", length=2, options={"fixed"=true})
     */
    private $language;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $backend;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $page;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $navigation;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $forward;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $custom1;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $custom2;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $custom3;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $custom4;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $custom5;

    /**
     * @param array $fields
     */
    public function __construct(array $fields = array())
    {
        $this->setMapping($fields);
    }

    /**
     * @param array $fields
     *
     * @return $this
     */
    public function setMapping(array $fields = array())
    {
        $allowedFields = array('backend', 'page', 'navigation', 'customDate', 'forward', 'custom1', 'custom2', 'custom3', 'custom4', 'custom5');
        foreach ($fields as $field => $value) {
            if (!$value || !in_array($field, $allowedFields)) {
                continue;
            }
            $this->$field = $value;
        }

        return $this;
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
     * @return ElementVersion
     */
    public function getElementVersion()
    {
        return $this->elementVersion;
    }

    /**
     * @param ElementVersion $elementVersion
     *
     * @return $this
     */
    public function setElementVersion(ElementVersion $elementVersion)
    {
        $this->elementVersion = $elementVersion;
        $elementVersion->addMappedField($this);

        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     *
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return string
     */
    public function getBackend()
    {
        return $this->backend;
    }

    /**
     * @param string $backend
     *
     * @return $this
     */
    public function setBackend($backend)
    {
        $this->backend = $backend;

        return $this;
    }

    /**
     * @return string
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param string $page
     *
     * @return $this
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return string
     */
    public function getNavigation()
    {
        return $this->navigation;
    }

    /**
     * @param string $navigation
     *
     * @return $this
     */
    public function setNavigation($navigation)
    {
        $this->navigation = $navigation;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     *
     * @return $this
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return string
     */
    public function getForward()
    {
        return $this->forward;
    }

    /**
     * @param string $forward
     *
     * @return $this
     */
    public function setForward($forward)
    {
        $this->forward = $forward;

        return $this;
    }

}