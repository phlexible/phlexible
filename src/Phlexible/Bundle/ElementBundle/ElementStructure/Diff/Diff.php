<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ElementStructure\Diff;

use Phlexible\Bundle\ElementBundle\Model\ElementStructure;
use Phlexible\Bundle\ElementBundle\Model\ElementStructureValue;

/**
 * Diff
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Diff
{
    private $added = array();
    private $modified = array();
    private $removed = array();

    /**
     * @param ElementStructure      $structure
     * @param ElementStructureValue $newValue
     *
     * @return $this
     */
    public function addAdded(ElementStructure $structure, ElementStructureValue $newValue)
    {
        $this->added[] = array('structure' => $structure, 'newValue' => $newValue);

        return $this;
    }

    /**
     * @return array
     */
    public function getAdded()
    {
        return $this->added;
    }

    /**
     * @param ElementStructure      $structure
     * @param ElementStructureValue $oldValue
     * @param ElementStructureValue $newValue
     *
     * @return $this
     */
    public function addModified(ElementStructure $structure, ElementStructureValue $oldValue, ElementStructureValue $newValue)
    {
        $this->modified[] = array('structure' => $structure, 'newValue' => $newValue, 'oldValue' => $oldValue);

        return $this;
    }

    /**
     * @return array
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @param ElementStructure      $structure
     * @param ElementStructureValue $oldValue
     *
     * @return $this
     */
    public function addRemoved(ElementStructure $structure, ElementStructureValue $oldValue)
    {
        $this->removed[] = array('structure' => $structure, 'oldValue' => $oldValue);

        return $this;
    }

    /**
     * @return array
     */
    public function getRemoved()
    {
        return $this->removed;
    }
}