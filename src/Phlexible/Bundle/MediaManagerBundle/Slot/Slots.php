<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Slot;

/**
 * Slot collection
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Slots
{
    /**
     * @var array
     */
    private $slots = array();

    /**
     * @param SlotInterface $slot
     *
     * @return $this
     */
    public function append(SlotInterface $slot)
    {
        array_push($this->slots, $slot);

        return $this;
    }

    /**
     * @param SlotInterface $slot
     *
     * @return $this
     */
    public function prepend(SlotInterface $slot)
    {
        array_unshift($this->slots, $slot);

        return $this;
    }

    /**
     * @return array
     */
    public function getAllData()
    {
        $data = array();

        foreach ($this->slots as $slot) {
            $data = array_merge($data, $slot->getData());
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->slots;
    }

    /**
     * @param string $key
     *
     * @return SlotInterface
     */
    public function getSlot($key)
    {
        foreach ($this->slots as $slot) {
            if ($slot->getKey() == $key) {
                return $slot;
            }
        }

        return null;
    }
}
