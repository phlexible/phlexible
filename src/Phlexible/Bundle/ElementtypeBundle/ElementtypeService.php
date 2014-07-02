<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle;
use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Phlexible\Bundle\ElementtypeBundle\Elementtype\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\Elementtype\ElementtypeRepository;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeStructure\ElementtypeStructure;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeStructure\ElementtypeStructureRepository;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeVersion\ElementtypeVersion;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeVersion\ElementtypeVersionRepository;
use Phlexible\Bundle\ElementtypeBundle\Viability\ViabilityManager;

/**
 * Elementtype service
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeService
{
    /**
     * @var ElementtypeRepository
     */
    private $elementtypeRepository;

    /**
     * @var ElementtypeVersionRepository
     */
    private $elementtypeVersionRepository;

    /**
     * @var ElementtypeStructureRepository
     */
    private $elementtypeStructureRepository;

    /**
     * @var ViabilityManager
     */
    private $viabilityManager;

    /**
     * @param ElementtypeRepository          $elementtypeRepository
     * @param ElementtypeVersionRepository   $elementtypeVersionRepository
     * @param ElementtypeStructureRepository $elementtypeStructureRepository
     * @param ViabilityManager               $viabilityManager
     */
    public function __construct(ElementtypeRepository $elementtypeRepository,
                                ElementtypeVersionRepository $elementtypeVersionRepository,
                                ElementtypeStructureRepository $elementtypeStructureRepository,
                                ViabilityManager $viabilityManager)
    {
        $this->elementtypeRepository = $elementtypeRepository;
        $this->elementtypeVersionRepository = $elementtypeVersionRepository;
        $this->elementtypeStructureRepository = $elementtypeStructureRepository;
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
        return $this->elementtypeRepository->find($elementTypeId);
    }

    /**
     * Find element type by unique ID
     *
     * @param string $uniqueID
     * @return Elementtype
     */
    public function findElementtypeByUniqueID($uniqueID)
    {
        return $this->elementtypeRepository->findByUniqueID($uniqueID);
    }

    /**
     * Find element types by type
     *
     * @param string $type
     * @return Elementtype[]
     */
    public function findElementtypeByType($type)
    {
        return $this->elementtypeRepository->findByType($type);
    }

    /**
     * Find all element types
     *
     * @return Elementtype[]
     */
    public function findAllElementtypes()
    {
        return $this->elementtypeRepository->findAll();
    }

    /**
     * Get all Element Type IDs
     *
     * @return array
     */
    public function findAllElementtypeIDs()
    {
        $ids = array();
        foreach ($this->findAllElementtypes() as $elementtype)
        {
            $ids[] = $elementtype->getId();
        }
        return $ids;
    }

    /**
     * @param Elementtype $elementtype
     * @param $version
     * @return ElementtypeVersion
     */
    public function findElementtypeVersion(Elementtype $elementtype, $version)
    {
        $elementtypeVersion = $this->elementtypeVersionRepository->find($elementtype, $version);

        return $elementtypeVersion;
    }

    /**
     * @param Elementtype $elementtype
     * @return ElementtypeVersion
     */
    public function findLatestElementtypeVersion(Elementtype $elementtype)
    {
        $elementtypeVersion = $this->elementtypeVersionRepository->find($elementtype, $elementtype->getLatestVersion());

        return $elementtypeVersion;
    }

    /**
     * @param Elementtype $elementtype
     * @return array
     */
    public function getVersions(Elementtype $elementtype)
    {
        return $this->elementtypeVersionRepository->getVersions($elementtype);
    }

    /**
     * @param ElementtypeVersion $elementtypeVersion
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
            ->setCreatedAt(new \DateTime())
        ;

        $elementtypeVersion = new ElementtypeVersion();
        $elementtypeVersion
            ->setElementtype($elementtype)
            ->setVersion(0)
            ->setCreateUserId($elementtype->getCreateUserId())
            ->setCreatedAt($elementtype->getCreatedAt())
        ;

        $this->elementtypeRepository->save($elementtype);
        $this->elementtypeVersionRepository->save($elementtypeVersion);

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
    public function createElementtypeVersion(ElementtypeStructure $elementtypeStructure, $uniqueId, $title, $icon, $hideChildren, $defaultTab)
    {
        $elementtypeVersion = $elementtypeStructure->getElementtypeVersion();
        $elementtype = $elementtypeVersion->getElementtype();

        $elementtype
            ->setUniqueId($uniqueId)
            ->setTitle($title)
            ->setIcon($icon)
            ->setHideChildren($hideChildren)
            ->setDefaultTab($defaultTab)
            ->setLatestVersion($elementtypeVersion->getVersion())
        ;

        $this->elementtypeVersionRepository->save($elementtypeVersion);
        $this->elementtypeRepository->save($elementtype);
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
        $this->elementtypeRepository->delete($elementtype);
        return;

        $dispatcher = Brainbits_Event_Dispatcher::getInstance();
        $db = MWF_Registry::getContainer()->dbPool->default;

        $elementType        = $this->getById($elementTypeId);
        $elementTypeVersion = $elementType->getLatest();

        // post before event
        $event = new BeforeDeleteEvent($elementType);
        if (!$dispatcher->dispatch(ElementtypeEvents::BEFORE_DELETE, $event))
        {
            throw new ElementtypeException(
                'Delete canceled by callback.'
            );
        }

        $delete = true;

        if ($elementType->getType() == Elementtype::TYPE_REFERENCE)
        {
            $select = $db->select()
                         ->distinct()
                         ->from($db->prefix.'elementtype_structure', array('elementtype_id', $db->fn->expr('MAX(version) AS max_version')))
                         ->where('reference_id = ?', $elementTypeId)
                         ->group('elementtype_id');

            $result = $db->fetchAll($select);

            if (count($result))
            {
                $delete = false;

                $select = $db->select()
                             ->from($db->prefix . 'elementtype', 'latest_version')
                             ->where('elementtype_id = ?');

                foreach($result as $row)
                {
                    $latestElementTypeVersion = $db->fetchOne($select, $row['elementtype_id']);

                    if ($latestElementTypeVersion == $row['max_version'])
                    {
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
            ->setUniqueId($elementtype->getUniqueId() . '_' . Uuid::generate())
        ;

        $this->elementtypeRepository->save($elementtype);

        $sourceElementtypeVersion = $this->findLatestElementtypeVersion($sourceElementtype);
        $elementtypeVersion = $this->copyElementtypeVersion($sourceElementtypeVersion, $elementtype);

        return $elementtypeVersion;
    }

    public function copyElementtypeVersion(ElementtypeVersion $sourceElementtypeVersion,
                                           Elementtype $targetElementtype)
    {
        $elementtypeVersion = clone $sourceElementtypeVersion;
        $elementtypeVersion
            ->setElementtype($targetElementtype)
            ->setVersion(1)
        ;

        $this->elementtypeVersionRepository->save($elementtypeVersion);

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
                ->setModifyTime(new \DateTime())
            ;
        }

        $navigation = $elementtypeVersion->getNavigation();

        // TODO: fix ds_id in navigation
        // TODO: copy viability

        return $elementtypeVersion;
    }
}
