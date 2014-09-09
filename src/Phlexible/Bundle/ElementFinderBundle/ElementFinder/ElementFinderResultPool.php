<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder;

/**
 * Element finder result pool
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementFinderResultPool
{
    /**
     * @var array
     */
    private $items = array();

    /**
     * @var array
     */
    private $matchedTreeIds = array();

    /**
     * @var int
     */
    private $resultsPerPage;

    /**
     * @param int   $resultsPerPage
     * @param mixed $filter
     */
    public function __construct($resultsPerPage, $filter)
    {
        $this->resultsPerPage = $resultsPerPage;
        $this->filter = $filter;
    }

    /**
     * @return array
     */
    public function getMatchedTreeIds()
    {
        return $this->matchedTreeIds;
    }

    /**
     * @param array $matchedTreeIds
     *
     * @return $this
     */
    public function setMatchedTreeIds(array $matchedTreeIds)
    {
        $this->matchedTreeIds = $matchedTreeIds;

        return $this;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param array $items
     *
     * @return $this
     */
    public function setItems(array $items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @param array $values
     *
     * @return array
     */
    public function getFilteredItems(array $values = array())
    {
        if (!$this->filter || !count($values)) {
            return $this->getItems();
        }

        return $this->filter->filterItems($this->getItems(), $values);
    }

    public function rotationKram()
    {

        // if _poolSize is given (indicationg rotating teasers)
        // and _poolSize differs from _maxElements
        // and we are in frontend ($this->page != 0)
        if ($elementCatch->hasRotation() && ($elementCatch->getMaxResults() < count($result)) && $page) {
            $reducedResult = array();
            $resultKeys = array_keys($result);
            $resultSize = count($resultKeys);

            // get last remembered rotation position
            $pos = $this->getLastRotationPosition() % $resultSize;

            $size = min(
                $elementCatch->getPoolSize() ? : PHP_INT_MAX,
                $elementCatch->getMaxResults(),
                $resultSize
            );

            for ($i = 0; $i < $size; ++$i) {
                $key = $resultKeys[$pos];
                $reducedResult[$key] = $this->result[$key];
                $pos = ($pos + 1) % $resultSize;
            }

            // remember rotation position
            // TODO: store somewhere else
            $this->setLastRotationPosition($pos);

            $result = $reducedResult;
        }
    }

    /**
     * Get paginator object.
     *
     * @param int $page
     *
     * @return \Zend_Paginator
     */
    public function getPaginator($page = 1)
    {
        $paginator = \Zend_Paginator::factory($this->getFilteredItems());

        if (!$this->resultsPerPage) {
            $paginator->setItemCountPerPage(PHP_INT_MAX);
        } else {
            $paginator->setItemCountPerPage($this->resultsPerPage);
        }

        if ($page > 1) {
            $paginator->setCurrentPageNumber($page);
        }

        return $paginator;
    }

}