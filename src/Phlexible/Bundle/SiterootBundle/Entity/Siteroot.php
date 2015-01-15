<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Siteroot
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="siteroot")
 */
class Siteroot
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string", length=36, options={"fixed"=true})
     */
    private $id;

    /**
     * @var bool
     * @ORM\Column(name="is_default", type="boolean")
     */
    private $default = false;

    /**
     * @var array
     * @ORM\Column(type="json_array")
     */
    private $patterns = array();

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
     * @var \DateTime
     * @ORM\Column(name="modified_at", type="datetime")
     */
    private $modifiedAt;

    /**
     * @var  string
     * @ORM\Column(name="modify_user_id", type="string", length=36, options={"fixed"=true})
     */
    private $modifyUserId;

    /**
     * @var array
     * @ORM\Column(name="special_tids", type="json_array")
     */
    private $specialTids = [];

    /**
     * @var array
     * @ORM\Column(type="json_array")
     */
    private $titles = [];

    /**
     * @var array
     * @ORM\Column(type="json_array")
     */
    private $properties = [];

    /**
     * @var array
     * @ORM\Column(name="content_channels", type="json_array")
     */
    private $contentChannels = [];

    /**
     * @var Navigation[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Navigation", mappedBy="siteroot")
     */
    private $navigations;

    /**
     * @var Url[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Url", mappedBy="siteroot")
     */
    private $urls;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->navigations = new ArrayCollection();
        $this->urls = new ArrayCollection();
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param bool $default
     *
     * @return $this
     */
    public function setDefault($default)
    {
        $this->default = (bool) $default;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        return $this->default;
    }

    /**
     * @param array $patterns
     *
     * @return $this
     */
    public function setPatterns(array $patterns = array())
    {
        foreach ($patterns as $name => $pattern) {
            $this->setPattern($name, $pattern);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getPatterns()
    {
        return $this->patterns;
    }

    /**
     * @param string $name
     * @param string $pattern
     *
     * @return $this
     */
    public function setPattern($name, $pattern)
    {
        $this->patterns[$name] = $pattern;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getPattern($name)
    {
        if (!isset($this->patterns[$name])) {
            throw new \InvalidArgumentException("Siteroot pattern $name not found.");
        }

        return $this->patterns[$name];
    }

    /**
     * @param string $createUid
     *
     * @return $this
     */
    public function setCreateUserId($createUid)
    {
        $this->createUserId = $createUid;

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
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param string $modifyUid
     *
     * @return $this
     */
    public function setModifyUserId($modifyUid)
    {
        $this->modifyUserId = $modifyUid;

        return $this;
    }

    /**
     * @return string
     */
    public function getModifyUserId()
    {
        return $this->modifyUserId;
    }

    /**
     * @param \DateTime $modifiedAt
     *
     * @return $this
     */
    public function setModifiedAt(\DateTime $modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * Set all titles
     *
     * @param array $titles
     *
     * @return $this
     */
    public function setTitles(array $titles)
    {
        $this->titles = $titles;

        return $this;
    }

    /**
     * Return all titles
     *
     * @return array
     */
    public function getTitles()
    {
        return $this->titles;
    }

    /**
     * Set title
     *
     * @param string $language
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($language, $title)
    {
        $this->titles[$language] = $title;

        return $this;
    }

    /**
     * Return siteroot title
     *
     * @param string $language
     *
     * @return string
     */
    public function getTitle($language = null)
    {
        $fallbackLanguage = key($this->titles);
        if ($language === null) {
            $language = $fallbackLanguage;
        }

        if (!empty($this->titles[$language])) {
            return $this->titles[$language];
        }

        if (!empty($this->titles[$fallbackLanguage])) {
            return $this->titles[$fallbackLanguage];
        }

        $title = false;
        try {
            $defaultUrl = $this->getDefaultUrl();

            if ($defaultUrl) {
                $title = $defaultUrl->getHostname();
            }
        } catch (\Exception $e) {
        }

        if (!$title) {
            return '(No title)';
        }

        return $title;
    }

    /**
     * @return Navigation[]
     */
    public function getNavigations()
    {
        return $this->navigations;
    }

    /**
     * @param Navigation $navigation
     *
     * @return $this
     */
    public function addNavigation(Navigation $navigation)
    {
        if (!$this->navigations->contains($navigation)) {
            $this->navigations->add($navigation);
            $navigation->setSiteroot($this);
        }

        return $this;
    }

    /**
     * @param Navigation $navigation
     *
     * @return $this
     */
    public function removeNavigation(Navigation $navigation)
    {
        if ($this->navigations->contains($navigation)) {
            $this->navigations->removeElement($navigation);
            $navigation->setSiteroot(null);
        }

        return $this;
    }

    /**
     * Return all special tids
     *
     * @return array
     */
    public function getSpecialTids()
    {
        return $this->specialTids;
    }

    /**
     * @param array $specialTids
     *
     * @return $this
     */
    public function setSpecialTids(array $specialTids)
    {
        $this->specialTids = $specialTids;

        return $this;
    }

    /**
     * Return special tids for a language
     *
     * @param string $language
     *
     * @return array
     */
    public function getSpecialTidsForLanguage($language = null)
    {
        $specialTids = [];

        foreach ($this->specialTids as $specialTid) {
            if ($specialTid['language'] === $language || $specialTid['language'] === null) {
                $specialTids[$specialTid['name']] = $specialTid['treeId'];
            }
        }

        return $specialTids;
    }

    /**
     * Return a special tid
     *
     * @param string $language
     * @param string $key
     *
     * @return string
     */
    public function getSpecialTid($language, $key)
    {
        $languageSpecialTids = $this->getSpecialTidsForLanguage($language);

        if (!empty($languageSpecialTids[$key])) {
            return $languageSpecialTids[$key];
        }

        return null;
    }

    /**
     * @param Url $url
     *
     * @return $this
     */
    public function addUrl(Url $url)
    {
        if (!$this->urls->contains($url)) {
            $this->urls->add($url);
            $url->setSiteroot($this);
        }

        return $this;
    }

    /**
     * @param Url $url
     *
     * @return $this
     */
    public function removeUrl(Url $url)
    {
        if ($this->urls->contains($url)) {
            $this->urls->removeElement($url);
            $url->setSiteroot(null);
        }

        return $this;
    }

    /**
     * @return Url[]
     */
    public function getUrls()
    {
        return $this->urls;
    }

    /**
     * Return the default url
     *
     * @param string $language
     *
     * @return Url
     */
    public function getDefaultUrl($language = null)
    {
        foreach ($this->urls as $url) {
            if ($url->isDefault()) {
                return $url;
            }
        }

        return null;
    }

    /**
     * @param array $properties
     *
     * @return $this
     */
    public function setProperties(array $properties)
    {
        $this->properties = $properties;

        return $this;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        $properties = $this->properties;

        return $properties;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function setProperty($key, $value)
    {
        $this->properties[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function getProperty($key)
    {
        if (empty($this->properties[$key])) {
            return null;
        }

        return $this->properties[$key];
    }

    /**
     * @param array $contentChannels
     *
     * @return $this
     */
    public function setContentChannels(array $contentChannels)
    {
        $this->contentChannels = $contentChannels;

        return $this;
    }

    /**
     * @return array
     */
    public function getContentChannelIds()
    {
        if (null === $this->contentChannels) {
            return [];
        }

        return array_keys($this->contentChannels);
    }

    /**
     * @return int|null|string
     */
    public function getDefaultContentChannelId()
    {
        if (null === $this->contentChannels) {
            return null;
        }

        foreach ($this->contentChannels as $contentChannelId => $default) {
            if ($default) {
                return $contentChannelId;
            }
        }

        return null;
    }
}
