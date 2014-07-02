<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Data source
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="datasource")
 */
class DataSource
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string", length=36, options={"fixed"=true})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(name="create_user_id", type="string", length=36, options={"fixed"=true})
     */
    private $createUserId;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     * @ORM\Column(name="modify_user_id", type="string", length=36, options={"fixed"=true})
     */
    private $modifyUserId;

    /**
     * @var \DateTime
     * @ORM\Column(name="modified_at", type="datetime")
     */
    private $modifiedAt;

    /**
     * @var DataSourceValue[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="DataSourceValue", mappedBy="datasource")
     */
    private $values;

    /**
     * @var string
     */
    private $language;

    /**
     * @var array
     */
    private $activeKeys = array();

    /**
     * @var array
     */
    private $inactiveKeys = array();

    public function __construct()
    {
        $this->values = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

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
     * @return \DateTime
     */
    public function getModifyUserId()
    {
        return $this->modifyUserId;
    }

    /**
     * @param string $modifyUserId
     *
     * @return $this
     */
    public function setModifyUserId($modifyUserId)
    {
        $this->modifyUserId = $modifyUserId;

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
     * @param \DateTime $modifyTime
     *
     * @return $this
     */
    public function setModifiedAt(\DateTime $modifyTime)
    {
        $this->modifiedAt = $modifyTime;

        return $this;
    }

    /**
     * @return DataSourceValue[]|ArrayCollection
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param DataSourceValue $value
     *
     * @return $this
     */
    public function addValue(DataSourceValue $value)
    {
        if (!$this->values->contains($value)) {
            $this->values->add($value);
            $value->setDatasource($this);
        }

        return $this;
    }

    /**
     * @param DataSourceValue $value
     *
     * @return $this
     */
    public function removeValue(DataSourceValue $value)
    {
        if ($this->values->contains($value)) {
            $this->values->removeElement($value);
            $value->setDatasource(null);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getKeys()
    {
        return array_merge($this->activeKeys, $this->inactiveKeys);
    }

    /**
     * @return array
     */
    public function getActiveKeys()
    {
        return $this->activeKeys;
    }

    /**
     * @return array
     */
    public function getInactiveKeys()
    {
        return $this->inactiveKeys;
    }

    /**
     * @param array $keys
     *
     * @return $this
     */
    public function setKeys(array $keys)
    {
        $this->setActiveKeys($keys);

        return $this;
    }

    /**
     * @param array $keys
     *
     * @return $this
     */
    public function setActiveKeys(array $keys)
    {
        $this->activeKeys = $keys;

        return $this;
    }

    /**
     * @param array $keys
     *
     * @return $this
     */
    public function setInactiveKeys(array $keys)
    {
        $this->inactiveKeys = $keys;

        return $this;
    }

    /**
     * @param string $key
     * @param bool   $active
     *
     * @return $this
     */
    public function addKey($key, $active = true)
    {
        $key = trim($key);

        if ($active) {
            $keyStore = & $this->activeKeys;
            $otherKeyStore = & $this->inactiveKeys;
        } else {
            $keyStore = & $this->inactiveKeys;
            $otherKeyStore = & $this->activeKeys;
        }

        // add to one key store
        if (!in_array($key, $keyStore)) {
            $keyStore[] = $key;
        }

        // remove from other key store
        $removableKey = array_search($key, $otherKeyStore);
        if (false !== $removableKey) {
            unset($otherKeyStore[$removableKey]);
        }

        return $this;
    }

    /**
     * Remove keys from data source.
     *
     * @param array $removeableKeys
     *
     * @return $this
     */
    public function removeKeys($removeableKeys)
    {
        $this->activeKeys = array_diff($this->activeKeys, $removeableKeys);
        $this->inactiveKeys = array_diff($this->inactiveKeys, $removeableKeys);

        return $this;
    }

    /**
     * Remove keys from data source.
     *
     * @param array $deactivatableKeys
     *
     * @return $this
     */
    public function deactivateKeys($deactivatableKeys)
    {
        $oldActiveKeys = $this->activeKeys;

        $this->activeKeys = array_diff($this->activeKeys, $deactivatableKeys);

        $this->inactiveKeys = array_merge(
            $this->inactiveKeys,
            array_diff($oldActiveKeys, $this->activeKeys)
        );

        return $this;
    }

    /**
     * Remove keys from data source.
     *
     * @param array $activatableKeys
     *
     * @return $this
     */
    public function activateKeys($activatableKeys)
    {
        $oldInactiveKeys = $this->inactiveKeys;

        $this->inactiveKeys = array_diff($this->inactiveKeys, $activatableKeys);

        $this->activeKeys = array_merge(
            $this->activeKeys,
            array_diff($oldInactiveKeys, $this->inactiveKeys)
        );

        return $this;
    }
}
