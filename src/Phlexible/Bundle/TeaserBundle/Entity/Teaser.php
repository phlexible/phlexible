<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Phlexible\Component\Identifier\IdentifiableInterface;
use Phlexible\Bundle\TeaserBundle\Teaser\TeaserIdentifier;

/**
 * Teaser
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="teaser")
 */
class Teaser implements IdentifiableInterface
{
    const TYPE_TEASER    = 'teaser';
    const TYPE_CATCH     = 'catch';
    const TYPE_INHERITED = 'inherited';
    const TYPE_STOP      = 'stop';
    const TYPE_HIDE      = 'hide';

    /**
     * Node Type: element
     */
    const TYPE_ELEMENT = 'element';

    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="tree_id", type="integer")
     */
    private $treeId;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $eid;

    /**
     * @var int
     * @ORM\Column(name="layoutarea_id", type="integer")
     */
    private $layoutareaId;

    /**
     * @var string
     * @ORM\Column(type="string", length=20)
     */
    private $type;

    /**
     * @var int
     * @ORM\Column(name="type_id", type="integer", nullable=true)
     */
    private $typeId;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $sort;

    /**
     * @var string
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $attributes;

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
     * @return TeaserIdentifier
     */
    public function getIdentifier()
    {
        return new TeaserIdentifier($this->id);
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
     * @return int
     */
    public function getTreeId()
    {
        return $this->treeId;
    }

    /**
     * @param int $treeId
     *
     * @return $this
     */
    public function setTreeId($treeId)
    {
        $this->treeId = $treeId;

        return $this;
    }

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
     * @return int
     */
    public function getLayoutareaId()
    {
        return $this->layoutareaId;
    }

    /**
     * @param int $layoutareaId
     *
     * @return $this
     */
    public function setLayoutareaId($layoutareaId)
    {
        $this->layoutareaId = $layoutareaId;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return int
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * @param int $typeId
     *
     * @return $this
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;

        return $this;
    }

    /**
     * @return int
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param int $sort
     *
     * @return $this
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($key, $default = null)
    {
        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeAttribute($key)
    {
        if (isset($this->attributes[$key])) {
            unset($this->attributes[$key]);
        }

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
     * @return boolean
     */
    public function getStopInherit()
    {
        return $this->getAttribute('stopInherit', false);
    }

    /**
     * @param boolean $stopInherit
     *
     * @return $this
     */
    public function setStopInherit($stopInherit)
    {
        return $this->setAttribute('stopInherit', $stopInherit);
    }

    /**
     * @return boolean
     */
    public function getNoDisplay()
    {
        return $this->getAttribute('noDisplay', false);
    }

    /**
     * @param boolean $noDisplay
     *
     * @return $this
     */
    public function setNoDisplay($noDisplay)
    {
        return $this->setAttribute('noDisplay', $noDisplay);
    }

    /**
     * {@inheritdoc}
     */
    public function getCache()
    {
        return $this->getAttribute('cache', array());
    }

    /**
     * {@inheritdoc}
     */
    public function setCache($cache)
    {
        return $this->setAttribute('cache', $cache);
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->getAttribute('controller');
    }

    /**
     * @param string $controller
     *
     * @return $this
     */
    public function setController($controller)
    {
        return $this->setAttribute('controller', $controller);
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->getAttribute('template');
    }

    /**
     * @param string $template
     *
     * @return $this
     */
    public function setTemplate($template)
    {
        return $this->setAttribute('template', $template);
    }
}
