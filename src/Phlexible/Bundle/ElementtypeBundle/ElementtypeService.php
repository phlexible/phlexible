<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle;

use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeManagerInterface;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructure;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructureNode;
use Phlexible\Bundle\ElementtypeBundle\Model\ViabilityManagerInterface;
use Phlexible\Bundle\ElementtypeBundle\Usage\UsageManager;
use Phlexible\Bundle\GuiBundle\Util\Uuid;

/**
 * Elementtype service
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeService
{
    /**
     * @var ElementtypeManagerInterface
     */
    private $elementtypeManager;

    /**
     * @var ViabilityManagerInterface
     */
    private $viabilityManager;

    /**
     * @var UsageManager
     */
    private $usageManager;

    /**
     * @param ElementtypeManagerInterface $elementtypeManager
     * @param ViabilityManagerInterface   $viabilityManager
     * @param UsageManager                $usageManager
     */
    public function __construct(
        ElementtypeManagerInterface $elementtypeManager,
        ViabilityManagerInterface $viabilityManager,
        UsageManager $usageManager)
    {
        $this->elementtypeManager = $elementtypeManager;
        $this->viabilityManager = $viabilityManager;
        $this->usageManager = $usageManager;
    }

    /**
     * Find element type by ID
     *
     * @param int $elementtypeId
     *
     * @return Elementtype
     */
    public function findElementtype($elementtypeId)
    {
        return $this->elementtypeManager->find($elementtypeId);
    }

    /**
     * Find element type by unique ID
     *
     * @param string $type
     *
     * @return Elementtype[]
     */
    public function findElementtypeByType($type)
    {
        $elementtypes = array();
        foreach ($this->elementtypeManager->findAll() as $elementtype) {
            if ($elementtype->getType() === $type) {
                $elementtypes[] = $elementtype;
            }
        }

        return $elementtypes;
    }

    /**
     * Find all element types
     *
     * @return Elementtype[]
     */
    public function findAllElementtypes()
    {
        return $this->elementtypeManager->findAll();
    }

    /**
     * @param Elementtype $elementtype
     *
     * @return Elementtype[]
     */
    public function findAllowedParents(Elementtype $elementtype)
    {
        $elementtypes = array();
        foreach ($this->viabilityManager->findAllowedParents($elementtype) as $viability) {
            $elementtypes[] = $this->findElementtype($viability->getUnderElementtypeId());
        }

        return $elementtypes;
    }

    /**
     * @param Elementtype $elementtype
     *
     * @return Elementtype[]
     */
    public function findAllowedChildren(Elementtype $elementtype)
    {
        $elementtypes = array();
        foreach ($this->viabilityManager->findAllowedChildren($elementtype) as $viability) {
            $elementtypes[] = $this->findElementtype($viability->getElementtypeId());
        }

        return $elementtypes;
    }

    /**
     * @param Elementtype $referenceElementtype
     *
     * @return Elementtype[]
     */
    public function findElementtypesUsingReferenceElementtype(Elementtype $referenceElementtype)
    {
        // TODO: references
        return array();//$this->elementtypeStructureManager->findElementtypesUsingReferenceElementtype($referenceElementtype);
    }

    /**
     * Create a new empty Element Type
     *
     * @param string               $type
     * @param string               $uniqueId
     * @param string               $name
     * @param string               $icon
     * @param ElementtypeStructure $elementtypeStructure
     * @param array                $mappings
     * @param string               $userId
     * @param bool                 $flush
     *
     * @return Elementtype
     */
    public function createElementtype($type, $uniqueId, $name, $icon, ElementtypeStructure $elementtypeStructure = null, array $mappings = null, $userId, $flush = true)
    {
        if (!$icon) {
            $icons = array(
                Elementtype::TYPE_FULL            => 'artikel_list.gif',
                Elementtype::TYPE_STRUCTURE       => 'nav_haupt.gif',
                Elementtype::TYPE_LAYOUTAREA      => '_fallback.gif',
                Elementtype::TYPE_LAYOUTCONTAINER => '_fallback.gif',
                Elementtype::TYPE_PART            => 'teaser_hellblau_list.gif',
                Elementtype::TYPE_REFERENCE       => '_fallback.gif',
            );

            $icon = $icons[$type];
        }

        $elementtype = new Elementtype();
        $elementtype
            ->setUniqueId($uniqueId)
            ->setType($type)
            ->setTitle('de', $name)
            ->setTitle('en', $name)
            ->setIcon($icon)
            ->setRevision(1)
            ->setStructure($elementtypeStructure)
            ->setMappings($mappings)
            ->setCreateUserId($userId)
            ->setCreatedAt(new \DateTime())
            ->setModifyUserId($elementtype->getCreateUserId())
            ->setModifiedAt($elementtype->getCreatedAt());

        $this->elementtypeManager->updateElementtype($elementtype);

        return $elementtype;
    }

    /**
     * @param Elementtype $elementtype
     */
    public function updateElementtype(Elementtype $elementtype)
    {
        $this->elementtypeManager->updateElementtype($elementtype);
    }

    /**
     * @param Elementtype $elementtype
     * @param array       $parentIds
     */
    public function updateViability(Elementtype $elementtype, array $parentIds)
    {
        $this->viabilityManager->updateViability($elementtype, $parentIds);
    }

    /**
     * Delete an Element Type
     *
     * @param Elementtype $elementtype
     */
    public function deleteElementtype(Elementtype $elementtype)
    {
        $this->elementtypeManager->deleteElementtype($elementtype);
    }

    /**
     * Duplicate an elementtype
     *
     * @param Elementtype $sourceElementtype
     * @param string      $userId
     *
     * @return Elementtype
     */
    public function duplicateElementtype(Elementtype $sourceElementtype, $userId)
    {
        $uniqId = uniqid();

        $elementtype = clone $sourceElementtype;

        foreach ($elementtype->getTitles() as $language => $title) {
          $elementtype->setTitle($language, $title . ' - copy - ' . $uniqId);
        }

        $elementtype
            ->setId(null)
            ->setRevision(1)
            ->setCreatedAt(new \DateTime())
            ->setCreateUserId($userId);

        $elementtypeStructure = new ElementtypeStructure();

        $rii = new \RecursiveIteratorIterator($sourceElementtype->getStructure(), \RecursiveIteratorIterator::SELF_FIRST);

        $dsIdMap = array();
        foreach ($rii as $sourceNode) {
            /* @var $sourceNode ElementtypeStructureNode */
            $node = clone $sourceNode;

            $dsIdMap[$sourceNode->getDsId()] = $dsId = Uuid::generate();

            $parentDsId = null;
            if (!$sourceNode->isRoot()) {
                $parentDsId = $dsIdMap[$sourceNode->getParentNode()->getDsId()];
            }

            $node
                ->setDsId($dsId)
                ->setParentDsId($parentDsId);

            $elementtypeStructure->addNode($node);
        }

        $this->elementtypeManager->updateElementtype($elementtype);

        return $elementtype;
    }
}
