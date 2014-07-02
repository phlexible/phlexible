<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Elementtype;

/**
 * Elementtype collection
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeCollection
{
    /**
     * @var Elementtype[]
     */
    private $elementtypes = array();

    /**
     * @var array
     */
    private $uniqueIdMap = array();

    /**
     * @var array
     */
    private $typeMap = array();

    /**
     * @param Elementtype[] $elementtypes
     */
    public function __construct(array $elementtypes = array())
    {
        foreach ($elementtypes as $elementtype)
        {
            $this->add($elementtype);
        }
    }

    /**
     * @param Elementtype $elementtype
     * @return $this
     */
    public function add(Elementtype $elementtype)
    {
        $this->elementtypes[$elementtype->getId()] = $elementtype;
        $this->uniqueIdMap[$elementtype->getUniqueId()] = $elementtype->getId();
        $this->typeMap[$elementtype->getType()][] = $elementtype->getId();

        return $this;
    }

    /**
     * @param int $id
     *
     * @return Elementtype|null
     */
    public function get($id)
    {
        if (isset($this->elementtypes[$id])) {
            return $this->elementtypes[$id];
        }

        return null;
    }

    /**
     * @param int $uniqueId
     *
     * @return Elementtype|null
     */
    public function getByUniqueId($uniqueId)
    {
        if (isset($this->uniqueIdMap[$uniqueId])) {
            return $this->get($this->uniqueIdMap[$uniqueId]);
        }

        return null;
    }

    /**
     * @param string $type
     * @return Elementtype[]
     */
    public function getByType($type)
    {
        $elementtypes = array();

        if (isset($this->typeMap[$type])) {
            foreach ($this->typeMap[$type] as $id) {
                $elementtypes[] = $this->get($id);
            }
        }

        return $elementtypes;
    }

    /**
     * @return Elementtype[]
     */
    public function getAll()
    {
        return $this->elementtypes;
    }
}
