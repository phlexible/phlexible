<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle;

use Phlexible\Bundle\ElementBundle\ElementVersion\FieldMapper;
use Phlexible\Bundle\ElementBundle\Entity\Element;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\ElementBundle\Model\ElementHistoryManagerInterface;
use Phlexible\Bundle\ElementBundle\Model\ElementManagerInterface;
use Phlexible\Bundle\ElementBundle\Model\ElementStructure;
use Phlexible\Bundle\ElementBundle\Model\ElementStructureManagerInterface;
use Phlexible\Bundle\ElementBundle\Model\ElementVersionManagerInterface;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeService;
use Phlexible\Bundle\ElementtypeBundle\Entity\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\Entity\ElementtypeVersion;

/**
 * Element service
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementService
{
    /**
     * @var ElementManagerInterface
     */
    private $elementManager;

    /**
     * @var ElementVersionManagerInterface
     */
    private $elementVersionManager;

    /**
     * @var ElementStructureManagerInterface
     */
    private $elementStructureManager;

    /**
     * @var ElementtypeService
     */
    private $elementtypeService;

    /**
     * @var ElementHistoryManagerInterface
     */
    private $elementHistoryManager;

    /**
     * @var FieldMapper
     */
    private $fieldMapper;

    /**
     * @param ElementManagerInterface          $elementManager
     * @param ElementVersionManagerInterface   $elementVersionManager
     * @param ElementStructureManagerInterface $elementStructureManager
     * @param ElementtypeService               $elementtypeService
     * @param ElementHistoryManagerInterface   $elementHistoryManager
     * @param FieldMapper                      $fieldMapper
     */
    public function __construct(
        ElementManagerInterface $elementManager,
        ElementVersionManagerInterface $elementVersionManager,
        ElementStructureManagerInterface $elementStructureManager,
        ElementtypeService $elementtypeService,
        ElementHistoryManagerInterface $elementHistoryManager,
        FieldMapper $fieldMapper)
    {
        $this->elementManager = $elementManager;
        $this->elementVersionManager = $elementVersionManager;
        $this->elementStructureManager = $elementStructureManager;
        $this->elementtypeService = $elementtypeService;
        $this->elementHistoryManager = $elementHistoryManager;
        $this->fieldMapper = $fieldMapper;
    }

    /**
     * @return ElementtypeService
     */
    public function getElementtypeService()
    {
        return $this->elementtypeService;
    }

    /**
     * Find element by ID
     *
     * @param int $eid
     *
     * @return Element
     */
    public function findElement($eid)
    {
        return $this->elementManager->find($eid);
    }

    /**
     * Find element by unique ID
     *
     * @param string $uniqueID
     *
     * @return Element
     */
    public function findElementByUniqueID($uniqueID)
    {
        return $this->elementManager->findByUniqueID($uniqueID);
    }

    /**
     * @param Element $element
     * @param int     $version
     *
     * @return ElementVersion
     */
    public function findElementVersion(Element $element, $version)
    {
        $elementVersion = $this->elementVersionManager->find($element, $version);

        return $elementVersion;
    }

    /**
     * @param Element $element
     *
     * @return ElementVersion
     */
    public function findLatestElementVersion(Element $element)
    {
        $elementVersion = $this->elementVersionManager->find($element, $element->getLatestVersion());

        return $elementVersion;
    }

    /**
     * @param Elementtype $elementtype
     *
     * @return Element[]
     */
    public function findElementsByElementtype(Elementtype $elementtype)
    {
        return $this->elementManager->findBy(array('elementtype' => $elementtype));
    }

    /**
     * @param Element $element
     * @param array   $languages
     *
     * @return ElementVersion
     */
    public function findOnlineLanguage(Element $element, $languages)
    {
        // TODO: fetch online language
        return current($languages);
    }

    /**
     * @param Element $element
     * @param string  $language
     *
     * @return ElementVersion
     */
    public function findOnlineElementVersion(Element $element, $language)
    {
        // TODO: fetch online version
        return $this->findLatestElementVersion($element);
    }

    /**
     * @param Element $element
     *
     * @return array
     */
    public function getVersions(Element $element)
    {
        return $this->elementVersionManager->getVersions($element);
    }

    /**
     * @param ElementVersion $elementVersion
     * @param string         $language
     *
     * @return ElementStructure
     */
    public function findElementStructure(ElementVersion $elementVersion, $language)
    {
        $elementVersionData = $this->elementStructureManager->find($elementVersion, $language);

        return $elementVersionData;
    }

    /**
     * @param Element $element
     *
     * @return Elementtype
     */
    public function findElementtype(Element $element)
    {
        return $this->elementtypeService->findElementtype($element->getElementtypeId());
    }

    /**
     * @param ElementVersion $elementVersion
     *
     * @return ElementtypeVersion
     */
    public function findElementtypeVersion(ElementVersion $elementVersion)
    {
        return $this->elementtypeService->findElementtypeVersion(
            $this->findElementtype($elementVersion->getElement()),
            $elementVersion->getElementtypeVersion()
        );
    }

    /**
     * @param ElementtypeVersion $elementtypeVersion
     * @param string             $masterLanguage
     * @param string             $userId
     *
     * @return Element
     */
    public function createElement(ElementtypeVersion $elementtypeVersion, $masterLanguage, $userId)
    {
        $element = new Element();
        $element
            ->setElementtype($elementtypeVersion->getElementtype())
            ->setMasterLanguage($masterLanguage)
            ->setLatestVersion(1)
            ->setCreateUserId($userId)
            ->setCreatedAt(new \DateTime());

        $elementVersion = new ElementVersion();
        $elementVersion
            ->setVersion(1)
            ->setElement($element)
            ->setElementtypeVersion($elementtypeVersion->getVersion())
            ->setCreateUserId($userId)
            ->setCreatedAt(new \DateTime());

        $this->fieldMapper->apply($elementVersion);

        $this->updateElement($element, false);
        $this->updateElementVersion($elementVersion);

        $this->elementHistoryManager->insert(
            ElementHistoryManagerInterface::ACTION_CREATE_ELEMENT,
            $element->getEid(),
            $userId,
            null,
            null,
            1,
            $masterLanguage
        );

        return $element;
    }

    /**
     * @param Element $element
     * @param bool    $flush
     */
    public function updateElement(Element $element, $flush = true)
    {
        $this->elementManager->updateElement($element, $flush);
    }

    /**
     * @param ElementVersion $elementVersion
     * @param bool           $flush
     */
    public function updateElementVersion(ElementVersion $elementVersion, $flush = true)
    {
        $this->elementVersionManager->updateElementVersion($elementVersion, $flush);
    }

    /**
     * @param ElementStructure $elementStructure
     * @param bool             $onlyValues
     */
    public function updateElementStructure(ElementStructure $elementStructure, $onlyValues = false)
    {
        $this->elementStructureManager->updateElementStructure($elementStructure, $onlyValues);
    }
}
