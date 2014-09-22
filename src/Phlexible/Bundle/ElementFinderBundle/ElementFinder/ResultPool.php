<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder;

/**
 * Result pool
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ResultPool implements \Countable
{
    /**
     * @var ResultItem[]
     */
    private $items = array();

    /**
     * @var mixed
     */
    private $filter;

    /**
     * @var string
     */
    private $query;

    /**
     * @param string $query
     *
     * @return $this
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param ResultItem[] $items
     *
     * @return $this
     */
    public function setItems(array $items)
    {
        $this->items = array();

        foreach ($items as $item) {
            $this->addItem($item);
        }

        return $this;
    }

    /**
     * @param ResultItem $item
     *
     * @return $this
     */
    public function addItem(ResultItem $item)
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * @param mixed $filter
     *
     * @return $this
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;

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
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * @param int $from
     * @param int $to
     *
     * @return array
     */
    public function range($from, $to)
    {
        return array_slice($this->items, $from, $to - $from);
    }

    /**
     * @param int $from
     * @param int $length
     *
     * @return array
     */
    public function slice($from, $length)
    {
        return array_slice($this->items, $from, $length);
    }

    /**
     * @param int $pageSize
     * @param int $page
     *
     * @return array
     */
    public function page($pageSize, $page)
    {
        return array_slice($this->items, $page * $pageSize, $pageSize);
    }

    /**
     * @param int $pageSize
     *
     * @return int
     */
    public function pageCount($pageSize)
    {
        return ceil(count($this->items) / $pageSize);
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
                $elementCatch->getPoolSize() ?: PHP_INT_MAX,
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
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->items);
    }
}