<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle;

use Phlexible\Bundle\ElementtypeBundle\Entity\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\Entity\ElementtypeStructureNode;
use Phlexible\Bundle\ElementtypeBundle\Entity\ElementtypeVersion;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeManagerInterface;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructure;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructureManagerInterface;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeVersionManagerInterface;
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
     * @var ElementtypeVersionManagerInterface
     */
    private $elementtypeVersionManager;

    /**
     * @var ElementtypeStructureManagerInterface
     */
    private $elementtypeStructureManager;

    /**
     * @var ViabilityManagerInterface
     */
    private $viabilityManager;

    /**
     * @var UsageManager
     */
    private $usageManager;

    /**
     * @param ElementtypeManagerInterface          $elementtypeManager
     * @param ElementtypeVersionManagerInterface   $elementtypeVersionManager
     * @param ElementtypeStructureManagerInterface $elementtypeStructureManager
     * @param ViabilityManagerInterface            $viabilityManager
     * @param UsageManager                         $usageManager
     */
    public function __construct(
        ElementtypeManagerInterface $elementtypeManager,
        ElementtypeVersionManagerInterface $elementtypeVersionManager,
        ElementtypeStructureManagerInterface $elementtypeStructureManager,
        ViabilityManagerInterface $viabilityManager,
        UsageManager $usageManager)
    {
        $this->elementtypeManager = $elementtypeManager;
        $this->elementtypeVersionManager = $elementtypeVersionManager;
        $this->elementtypeStructureManager = $elementtypeStructureManager;
        $this->viabilityManager = $viabilityManager;
        $this->usageManager = $usageManager;
    }

    /**
     * Find element type by ID
     *
     * @param int $elementTypeId
     *
     * @return Elementtype
     */
    public function findElementtype($elementTypeId)
    {
        return $this->elementtypeManager->find($elementTypeId);
    }

    /**
     * Find element type by unique ID
     *
     * @param string $uniqueID
     *
     * @return Elementtype
     */
    public function findElementtypeByUniqueID($uniqueID)
    {
        return $this->elementtypeManager->findByUniqueID($uniqueID);
    }

    /**
     * Find element types by type
     *
     * @param string $type
     *
     * @return Elementtype[]
     */
    public function findElementtypeByType($type)
    {
        return $this->elementtypeManager->findByType($type);
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
     * Get all Element Type IDs
     *
     * @return array
     */
    public function findAllElementtypeIDs()
    {
        $ids = array();
        foreach ($this->findAllElementtypes() as $elementtype) {
            $ids[] = $elementtype->getId();
        }

        return $ids;
    }

    /**
     * @param Elementtype $elementtype
     * @param             $version
     *
     * @return ElementtypeVersion
     */
    public function findElementtypeVersion(Elementtype $elementtype, $version)
    {
        $elementtypeVersion = $this->elementtypeVersionManager->find($elementtype, $version);

        return $elementtypeVersion;
    }

    /**
     * @param Elementtype $elementtype
     *
     * @return ElementtypeVersion
     */
    public function findLatestElementtypeVersion(Elementtype $elementtype)
    {
        $elementtypeVersion = $this->elementtypeVersionManager->find($elementtype, $elementtype->getLatestVersion());

        return $elementtypeVersion;
    }

    /**
     * @param Elementtype $elementtype
     *
     * @return array
     */
    public function getVersions(Elementtype $elementtype)
    {
        return $this->elementtypeVersionManager->getVersions($elementtype);
    }

    /**
     * @param ElementtypeVersion $elementtypeVersion
     *
     * @return ElementtypeStructure
     */
    public function findElementtypeStructure(ElementtypeVersion $elementtypeVersion)
    {
        $elementtypeStructure = $this->elementtypeStructureManager->find($elementtypeVersion);

        return $elementtypeStructure;
    }

    /**
     * @param Elementtype $elementtype
     *
     * @return array
     */
    public function findAllowedParentIds(Elementtype $elementtype)
    {
        return $this->viabilityManager->getAllowedParentIds($elementtype);
    }

    /**
     * @param Elementtype $elementtype
     *
     * @return array
     */
    public function findAllowedChildrenIds(Elementtype $elementtype)
    {
        return $this->viabilityManager->getAllowedChildrenIds($elementtype);
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
     * Create a new empty Element Type
     *
     * @param string $type
     * @param string $uniqueId
     * @param string $title
     * @param string $icon
     * @param string $userId
     * @param bool   $flush
     *
     * @return ElementtypeVersion
     */
    public function createElementtype($type, $uniqueId, $title, $icon, $userId, $flush = true)
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
            ->setTitle($title)
            ->setIcon($icon)
            ->setLatestVersion(1)
            ->setCreateUserId($userId)
            ->setCreatedAt(new \DateTime());

        $elementtypeVersion = new ElementtypeVersion();
        $elementtypeVersion
            ->setElementtype($elementtype)
            ->setVersion(1)
            ->setCreateUserId($elementtype->getCreateUserId())
            ->setCreatedAt($elementtype->getCreatedAt());

        $this->elementtypeManager->updateElementtype($elementtype, $flush);
        $this->elementtypeVersionManager->updateElementtypeVersion($elementtypeVersion, $flush);

        return $elementtypeVersion;
    }

    /**
     * @param Elementtype $elementtype
     * @param bool        $flush
     */
    public function updateElementtype(Elementtype $elementtype, $flush = true)
    {
        $this->elementtypeManager->updateElementtype($elementtype, $flush);
    }

    /**
     * @param ElementtypeVersion $elementtypeVersion
     * @param bool               $flush
     */
    public function updateElementtypeVersion(ElementtypeVersion $elementtypeVersion, $flush = true)
    {
        $this->elementtypeVersionManager->updateElementtypeVersion($elementtypeVersion, $flush);
    }

    /**
     * @param ElementtypeStructure $elementtypeStructure
     * @param bool                 $flush
     */
    public function updateElementtypeStructure(ElementtypeStructure $elementtypeStructure, $flush = true)
    {
        $this->elementtypeStructureManager->updateElementtypeStructure($elementtypeStructure, $flush);
    }

    /**
     * Delete an Element Type
     *
     * @param Elementtype $elementtype
     *
     * @return string
     */
    public function deleteElementtype(Elementtype $elementtype)
    {
        $usage = $this->usageManager->getUsage($elementtype);

        if ($usage) {
            $this->elementtypeManager->softDeleteElementtype($elementtype);

            return 'softdelete';
        }

        foreach ($this->getVersions($elementtype) as $version) {
            $elementtypeVersion = $this->findElementtypeVersion($elementtype, $version);
            $elementtypeStructure = $this->findElementtypeStructure($elementtypeVersion);

            $this->elementtypeStructureManager->deleteElementtypeStructure($elementtypeStructure, false);
            $this->elementtypeVersionManager->deleteElementtypeVersion($elementtypeVersion, false);
        }
        $this->elementtypeManager->deleteElementtype($elementtype);

        return 'delete';
    }

    /**
     * Duplicate an elementtype
     *
     * @param Elementtype $sourceElementtype
     * @param string      $userId
     *
     * @return ElementtypeVersion
     */
    public function duplicateElementtype(Elementtype $sourceElementtype, $userId)
    {
        $sourceElementtypeVersion = $this->findLatestElementtypeVersion($sourceElementtype);
        $sourceElementtypeStructure = $this->findElementtypeStructure($sourceElementtypeVersion);

        $uniqId = uniqid();

        $elementtype = clone $sourceElementtype;
        $elementtypeVersion = clone $sourceElementtypeVersion;

        $elementtype
            ->setId(null)
            ->setTitle($elementtype->getTitle() . ' - copy - ' . $uniqId)
            ->setUniqueId($elementtype->getUniqueId() . '_copy_' . $uniqId)
            ->setLatestVersion(1)
            ->setCreatedAt(new \DateTime())
            ->setCreateUserId($userId);

        $elementtypeVersion
            ->setId(null)
            ->setElementtype($elementtype)
            ->setVersion(1)
            ->setCreatedAt(new \DateTime())
            ->setCreateUserId($userId);

        $elementtypeStructure = new ElementtypeStructure();
        $elementtypeStructure
            ->setElementtypeVersion($elementtypeVersion);

        $rii = new \RecursiveIteratorIterator($sourceElementtypeStructure, \RecursiveIteratorIterator::SELF_FIRST);
        $idMap = array();
        $dsIdMap = array();
        foreach ($rii as $sourceNode) {
            /* @var $sourceNode ElementtypeStructureNode */
            $node = clone $sourceNode;

            $idMap[$sourceNode->getId()] = $node;
            $dsIdMap[$sourceNode->getDsId()] = $dsId = Uuid::generate();

            $parentNode = null;
            $parentDsId = null;
            if (!$sourceNode->isRoot()) {
                $parentNode = $idMap[$sourceNode->getParentNode()->getId()];
                $parentDsId = $dsIdMap[$sourceNode->getParentNode()->getDsId()];
            }

            $node
                ->setId(null)
                ->setDsId($dsId)
                ->setParentNode($parentNode)
                ->setParentDsId($parentDsId)
                ->setElementtype($elementtype)
                ->setVersion($elementtypeVersion->getVersion())
                ->setElementtypeStructure($elementtypeStructure);

            $elementtypeStructure->addNode($node);
        }

        $this->updateElementtype($elementtype, false);
        $this->updateElementtypeVersion($elementtypeVersion, false);
        $this->updateElementtypeStructure($elementtypeStructure, true);

        return $elementtypeVersion;
    }
}
