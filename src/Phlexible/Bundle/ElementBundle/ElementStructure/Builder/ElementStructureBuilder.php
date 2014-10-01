<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ElementStructure\Builder;

use Phlexible\Bundle\ElementBundle\ElementStructure\ElementStructure;
use Phlexible\Bundle\ElementBundle\ElementStructure\ElementStructureNode;
use Phlexible\Bundle\ElementBundle\Exception\InvalidArgumentException;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeStructure\ElementtypeStructure;

/**
 * Element structure builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementStructureBuilder
{
    /**
     * @var ElementtypeStructure
     */
    private $elementtypeStructure;

    /**
     * @var ElementStructure
     */
    private $elementStructure;

    /**
     * @var ElementStructureBuilder
     */
    private $parent;

    /**
     * @var string
     */
    private $dsId;

    /**
     * @var ElementStructureNode
     */
    private $node;

    /**
     * @param ElementtypeStructure    $elementtypeStructure
     * @param ElementStructure        $elementStructure
     * @param ElementStructureBuilder $parent
     */
    public function __construct(
        ElementtypeStructure $elementtypeStructure,
        ElementStructure $elementStructure = null,
        ElementStructureBuilder $parent = null)
    {
        $this->elementtypeStructure = $elementtypeStructure;

        if ($parent) {
            $this->parent = $parent;
        }

        if (!$elementStructure) {
            $elementStructure = new ElementStructure();
        }
        $this->elementStructure = $elementStructure;
    }

    /**
     * @param string $dsId
     * @param int    $id
     * @param string $name
     *
     * @return ElementStructureBuilder
     * @throws InvalidArgumentException
     */
    public function node($dsId, $id, $name)
    {
        if (!$this->elementtypeStructure->hasNode($dsId)) {
            throw new InvalidArgumentException("Node $dsId not found.");
        }
        $elementtypeNode = $this->elementtypeStructure->getNode($dsId);

        $elementNode = new ElementStructureNode();
        $this->node = $elementNode;
        $elementNode
            ->setId($id)
            ->setDsId($dsId)
            ->setName($name)
            ->setParentId($this->parent ? $this->parent->getNode()->getId() : null);

        $this->elementStructure->addNode($elementNode);

        $builder = new self($this->elementtypeStructure, $this->elementStructure, $this);

        return $builder;
    }

    /**
     * @return ElementStructureBuilder
     */
    public function end()
    {
        return $this->parent;
    }

    /**
     * @return string
     */
    public function getDsId()
    {
        return $this->dsId;
    }

    /**
     * @return ElementStructureNode
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @return ElementStructure
     */
    public function getElementStructure()
    {
        return $this->elementStructure;
    }
}
