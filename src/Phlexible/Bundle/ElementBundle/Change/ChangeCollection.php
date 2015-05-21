<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Change;

/**
 * Elementtype change collection
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ChangeCollection implements \Countable, \IteratorAggregate
{
    /**
     * @var ChangeInterface[]
     */
    private $changes = [];

    /**
     * @param ChangeInterface[] $changes
     */
    public function __construct(array $changes = [])
    {
        $this->changes = $changes;
    }

    /**
     * @param ChangeInterface $change
     *
     * @return $this
     */
    public function add(ChangeInterface $change)
    {
        $this->changes[] = $change;

        return $this;
    }

    /**
     * @return ChangeInterface[]
     */
    public function all()
    {
        return $this->changes;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->changes);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->changes, \ArrayIterator::ARRAY_AS_PROPS);
    }

    /**
     * @return ChangeCollection
     */
    public function filterAdd()
    {
        $changes = [];
        foreach ($this->changes as $change) {
            if ($change instanceof AddChange) {
                $changes[] = $change;
            }
        }

        return new self($changes);
    }

    /**
     * @return ChangeCollection
     */
    public function filterUpdate()
    {
        $changes = [];
        foreach ($this->changes as $change) {
            if ($change instanceof UpdateChange) {
                $changes[] = $change;
            }
        }

        return new self($changes);
    }

    /**
     * @return ChangeCollection
     */
    public function filterRemove()
    {
        $changes = [];
        foreach ($this->changes as $change) {
            if ($change instanceof RemoveChange) {
                $changes[] = $change;
            }
        }

        return new self($changes);
    }
}
