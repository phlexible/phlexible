<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle;

use Phlexible\Bundle\ElementtypeBundle\Entity\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\Entity\ElementtypeVersion;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeManagerInterface;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructure;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructureManagerInterface;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeVersionManagerInterface;
use Phlexible\Bundle\ElementtypeBundle\Model\ViabilityManagerInterface;
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
    private $elementtypeStructureRepository;

    /**
     * @var ViabilityManagerInterface
     */
    private $viabilityManager;

    /**
     * @param ElementtypeManagerInterface          $elementtypeManager
     * @param ElementtypeVersionManagerInterface   $elementtypeVersionManager
     * @param ElementtypeStructureManagerInterface $elementtypeStructureManager
     * @param ViabilityManagerInterface            $viabilityManager
     */
    public function __construct(
        ElementtypeManagerInterface $elementtypeManager,
        ElementtypeVersionManagerInterface $elementtypeVersionManager,
        ElementtypeStructureManagerInterface $elementtypeStructureManager,
        ViabilityManagerInterface $viabilityManager)
    {
        $this->elementtypeManager = $elementtypeManager;
        $this->elementtypeVersionManager = $elementtypeVersionManager;
        $this->elementtypeStructureRepository = $elementtypeStructureManager;
        $this->viabilityManager = $viabilityManager;
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
        $elementtypeStructure = $this->elementtypeStructureRepository->find($elementtypeVersion);

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
    public function saveAllowedParentIds(Elementtype $elementtype, array $parentIds)
    {
        $this->viabilityManager->saveAllowedParentIds($elementtype, $parentIds);
    }

    /**
     * Create a new Element Type
     *
     * @param string $type
     * @param string $uniqueId
     * @param string $title
     * @param string $icon
     * @param string $uid
     *
     * @return Elementtype
     */
    public function create($type, $uniqueId, $title, $icon, $uid)
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
            ->setCreateUserId($uid)
            ->setCreatedAt(new \DateTime());

        $elementtypeVersion = new ElementtypeVersion();
        $elementtypeVersion
            ->setElementtype($elementtype)
            ->setVersion(0)
            ->setCreateUserId($elementtype->getCreateUserId())
            ->setCreatedAt($elementtype->getCreatedAt());

        $this->elementtypeManager->save($elementtype);
        $this->elementtypeVersionManager->save($elementtypeVersion);

        return $elementtype;
    }

    /**
     * @param ElementtypeStructure $elementtypeStructure
     * @param string               $uniqueId
     * @param string               $title
     * @param string               $icon
     * @param bool                 $hideChildren
     * @param int                  $defaultTab
     */
    public function createElementtypeVersion(
        ElementtypeStructure $elementtypeStructure,
        $uniqueId,
        $title,
        $icon,
        $hideChildren,
        $defaultTab)
    {
        $elementtypeVersion = $elementtypeStructure->getElementtypeVersion();
        $elementtype = $elementtypeVersion->getElementtype();

        $elementtype
            ->setUniqueId($uniqueId)
            ->setTitle($title)
            ->setIcon($icon)
            ->setHideChildren($hideChildren)
            ->setDefaultTab($defaultTab)
            ->setLatestVersion($elementtypeVersion->getVersion());

        $this->elementtypeVersionManager->save($elementtypeVersion);
        $this->elementtypeManager->save($elementtype);
        $this->elementtypeStructureRepository->save($elementtypeStructure);
    }

    /**
     * Delete an Element Type
     *
     * @param int $elementTypeId
     */
    public function delete($elementTypeId)
    {
        $elementtype = $this->find($elementTypeId);
        $this->elementtypeManager->delete($elementtype);

        return;

        $dispatcher = Brainbits_Event_Dispatcher::getInstance();
        $db = MWF_Registry::getContainer()->dbPool->default;

        $elementType = $this->getById($elementTypeId);
        $elementTypeVersion = $elementType->getLatest();

        // post before event
        $event = new BeforeDeleteEvent($elementType);
        if (!$dispatcher->dispatch(ElementtypeEvents::BEFORE_DELETE, $event)) {
            throw new ElementtypeException(
                'Delete canceled by callback.'
            );
        }

        $delete = true;

        if ($elementType->getType() == Elementtype::TYPE_REFERENCE) {
            $select = $db->select()
                ->distinct()
                ->from(
                    $db->prefix . 'elementtype_structure',
                    array('elementtype_id', $db->fn->expr('MAX(version) AS max_version'))
                )
                ->where('reference_id = ?', $elementTypeId)
                ->group('elementtype_id');

            $result = $db->fetchAll($select);

            if (count($result)) {
                $delete = false;

                $select = $db->select()
                    ->from($db->prefix . 'elementtype', 'latest_version')
                    ->where('elementtype_id = ?');

                foreach ($result as $row) {
                    $latestElementTypeVersion = $db->fetchOne($select, $row['elementtype_id']);

                    if ($latestElementTypeVersion == $row['max_version']) {
                        throw new ElementtypeException('Reference in use, can\'t delete.');
                    }
                }
            }
        }
    }

    /**
     * Duplicate an elementtype
     *
     * @param Elementtype $sourceElementtype
     *
     * @return ElementtypeVersion
     */
    public function duplicateElementtype(Elementtype $sourceElementtype)
    {
        $elementtype = clone $sourceElementtype;
        $elementtype
            ->setId(null)
            ->setUniqueId($elementtype->getUniqueId() . '_' . Uuid::generate());

        $this->elementtypeManager->save($elementtype);

        $sourceElementtypeVersion = $this->findLatestElementtypeVersion($sourceElementtype);
        $elementtypeVersion = $this->copyElementtypeVersion($sourceElementtypeVersion, $elementtype);

        return $elementtypeVersion;
    }

    public function copyElementtypeVersion(
        ElementtypeVersion $sourceElementtypeVersion,
        Elementtype $targetElementtype)
    {
        $elementtypeVersion = clone $sourceElementtypeVersion;
        $elementtypeVersion
            ->setElementtype($targetElementtype)
            ->setVersion(1);

        $this->elementtypeVersionManager->save($elementtypeVersion);

        $sourceElementtypeStructure = $this->findElementtypeStructure($sourceElementtypeVersion);
        $elementtypeStructure = clone $sourceElementtypeStructure;
        $elementtypeStructure->setElementTypeVersion($elementtypeVersion);

        $dsIdMap = array();
        foreach ($elementtypeStructure->getIterator() as $node) {
            $dsIdMap[$node->getDsId()] = $newDsId = Uuid::generate();
            $node
                ->setDsId($newDsId)
                ->setParentDsId($dsIdMap[$node->getParentDsId()])
                ->setModifyUid($uid)
                ->setModifyTime(new \DateTime());
        }

        $navigation = $elementtypeVersion->getNavigation();

        // TODO: fix ds_id in navigation
        // TODO: copy viability

        return $elementtypeVersion;
    }
}
