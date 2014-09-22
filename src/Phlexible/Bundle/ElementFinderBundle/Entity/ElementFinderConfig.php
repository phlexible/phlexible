<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Element finder config
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="catch")
 */
class ElementFinderConfig
{
    const SORT_TITLE_BACKEND = '__backend_title';
    const SORT_TITLE_PAGE = '__page_title';
    const SORT_TITLE_NAVIGATION = '__navigation_title';
    const SORT_PUBLISH_DATE = '__publish_date';
    const SORT_CUSTOM_DATE = '__custom_date';

    const FIELD_SORT = 'sort_field';

    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @var int
     * @ORM\Column(name="tree_id", type="integer")
     */
    private $treeId;

    /**
     * @var array
     * @ORM\Column(name="elementtype_ids", type="simple_array")
     */
    private $elementtypeIds = array();

    /**
     * @var string
     * @ORM\Column(name="sort_field", type="string", length=255, nullable=true)
     */
    private $sortField;

    /**
     * @var string
     * @ORM\Column(name="sort_order", type="string", length=255, nullable=true)
     */
    private $sortDir;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $filter;

    /**
     * @var string
     * @ORM\Column(name="template", type="string", length=255, nullable=true)
     */
    private $template;

    /**
     * @var int
     * @ORM\Column(name="max_depth", type="integer")
     */
    private $maxDepth = -1;

    /**
     * @var bool
     * @ORM\Column(name="in_navigation", type="boolean")
     */
    private $inNavigation;

    /**
     * @var array
     * @ORM\Column(name="meta_search", type="string", length=255, nullable=true)
     */
    private $metaSearch;

    /**
     * @var array
     * @ORM\Column(name="create_user_id", type="string", length=36, options={"fixed"=true})
     */
    private $createUserId;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = (int) $id;

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
     * @param int $treeId
     *
     * @return $this
     */
    public function setTreeId($treeId)
    {
        $this->treeId = (int) $treeId;

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
     * @param array $elementtypeIds
     *
     * @return $this
     */
    public function setElementtypeIds(array $elementtypeIds)
    {
        $this->elementtypeIds = $elementtypeIds;

        return $this;
    }

    /**
     * @return array
     */
    public function getElementtypeIds()
    {
        return $this->elementtypeIds;
    }

    /**
     * @param string $sortField
     *
     * @return $this
     */
    public function setSortField($sortField)
    {
        $this->sortField = $sortField;

        return $this;
    }

    /**
     * Get field to sort by.
     *
     * @return string
     */
    public function getSortField()
    {
        return $this->sortField;
    }

    /**
     * @param string $sortOrder ASC/DESC
     *
     * @return $this
     */
    public function setSortDir($sortOrder)
    {
        $this->sortDir = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';

        return $this;
    }

    /**
     * @return string ASC/DESC
     */
    public function getSortDir()
    {
        return $this->sortDir;
    }

    /**
     * @param string $filter
     *
     * @return $this
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @param int $maxDepth
     *
     * @return $this
     */
    public function setMaxDepth($maxDepth)
    {
        $this->maxDepth = (int) $maxDepth;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxDepth()
    {
        return $this->maxDepth;
    }

    /**
     * @param bool $inNavigation
     *
     * @return $this
     */
    public function setNavigation($inNavigation = true)
    {
        $this->inNavigation = (bool) $inNavigation;

        return $this;
    }

    /**
     * @return bool
     */
    public function inNavigation()
    {
        return $this->inNavigation;
    }

    /**
     * @param string $template
     *
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param array $metaSearch
     *
     * @return $this
     */
    public function setMetaSearch(array $metaSearch = null)
    {
        $this->metaSearch = $metaSearch;

        return $this;
    }

    /**
     * @return array
     */
    public function getMetaSearch()
    {
        return $this->metaSearch;
    }
}
