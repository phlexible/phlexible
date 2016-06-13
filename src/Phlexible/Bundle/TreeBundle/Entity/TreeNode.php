<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Phlexible\Bundle\TreeBundle\Model\TreeInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Phlexible\Bundle\TreeBundle\Tree\TreeIterator;
use Phlexible\Component\AccessControl\Model\DomainObjectInterface;
use Phlexible\Component\AccessControl\Model\HierarchicalDomainObjectInterface;

/**
 * Tree node
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="tree")
 */
class TreeNode implements TreeNodeInterface, DomainObjectInterface, HierarchicalDomainObjectInterface
{
    /**
     * @var TreeInterface
     */
    private $tree;

    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var int
     * ORM\Column(name="parent_id", type="integer", nullable=true)
     * @ORM\ManyToOne(targetEntity="TreeNode")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parentNode;

    /**
     * @var string
     * @ORM\Column(name="siteroot_id", type="string", length=36, options={"fixed"=true})
     */
    private $siterootId;

    /**
     * @var string
     * @ORM\Column(type="string", length=50)
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
    private $sort = 0;

    /**
     * @var string
     * @ORM\Column(name="sort_mode", type="string", length=255)
     */
    private $sortMode = 'free';

    /**
     * @var string
     * @ORM\Column(name="sort_dir", type="string", length=255)
     */
    private $sortDir = 'asc';

    /**
     * @var string
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $attributes;

    /**
     * @var bool
     * @ORM\Column(name="in_navigation", type="boolean", options={"default"=0})
     */
    private $inNavigation = false;

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
     * Get an iterator for this node.
     *
     * @return TreeIterator
     */
    public function getIterator()
    {
        return new TreeIterator($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectIdentifier()
    {
        return $this->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectType()
    {
        return get_class($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getHierarchicalObjectIdentifiers()
    {
        return $this->getTree()->getIdPath($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getTree()
    {
        return $this->tree;
    }

    /**
     * {@inheritdoc}
     */
    public function setTree(TreeInterface $tree)
    {
        $this->tree = $tree;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isRoot()
    {
        return $this->getParentNode() === null;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentNode()
    {
        return $this->parentNode;
    }

    /**
     * {@inheritdoc}
     */
    public function setParentNode($parentNode)
    {
        $this->parentNode = $parentNode;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSiterootId()
    {
        return $this->siterootId;
    }

    /**
     * {@inheritdoc}
     */
    public function setSiterootId($siterootId)
    {
        $this->siterootId = $siterootId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * {@inheritdoc}
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * {@inheritdoc}
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSortMode()
    {
        return $this->sortMode;
    }

    /**
     * {@inheritdoc}
     */
    public function setSortMode($sortMode)
    {
        $this->sortMode = $sortMode;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSortDir()
    {
        return $this->sortDir;
    }

    /**
     * {@inheritdoc}
     */
    public function setSortDir($sortDir)
    {
        $this->sortDir = $sortDir;

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
    public function setAttributes(array $attributes = null)
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
     * {@inheritdoc}
     */
    public function getInNavigation()
    {
        return $this->inNavigation;
    }

    /**
     * {@inheritdoc}
     */
    public function setInNavigation($inNavigation)
    {
        $this->inNavigation = $inNavigation;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreateUserId()
    {
        return $this->createUserId;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreateUserId($createUserId)
    {
        $this->createUserId = $createUserId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCache()
    {
        return $this->getAttribute('cache', []);
    }

    /**
     * {@inheritdoc}
     */
    public function setCache($cache)
    {
        if ($cache) {
            $this->setAttribute('cache', $cache);
        } else {
            $this->removeAttribute('cache');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getController()
    {
        return $this->getAttribute('controller');
    }

    /**
     * {@inheritdoc}
     */
    public function setController($controller)
    {
        if ($controller) {
            $this->setAttribute('controller', $controller);
        } else {
            $this->removeAttribute('controller');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate()
    {
        return $this->getAttribute('template');
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplate($template)
    {
        if ($template) {
            $this->setAttribute('template', $template);
        } else {
            $this->removeAttribute('template');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoutes()
    {
        return $this->getAttribute('routes', []);
    }

    /**
     * {@inheritdoc}
     */
    public function setRoutes(array $routes = null)
    {
        if ($routes) {
            $this->setAttribute('routes', $routes);
        } else {
            $this->removeAttribute('routes');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getNeedAuthentication()
    {
        return $this->getAttribute('needAuthentication', false);
    }

    /**
     * {@inheritdoc}
     */
    public function setNeedAuthentication($needsAuthentication)
    {
        if ($needsAuthentication) {
            $this->setAttribute('needAuthentication', true);
        } else {
            $this->removeAttribute('needAuthentication');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSecurityExpression()
    {
        $security = $this->getAttribute('security');
        if (!$security) {
            return 'true';
        }

        if (!empty($security['expression'])) {
            $expression = $security['expression'];
        } else {
            $expressions = array();
            if (!empty($security['authenticationRequired'])) {
                $expressions[] = 'is_fully_authenticated()';
            }
            if (!empty($security['roles'])) {
                $security['roles'] = (array) $security['roles'];
                foreach ($security['roles'] as $role) {
                    $expressions[] = "has_role('$role')";
                }
            }
            if (!empty($security['query_acl'])) {
                $expressions[] = "is_granted('VIEW', node)";
            }

            $expression = implode(' and ', $expressions);
        }

        return $expression ?: 'true';
    }
}
