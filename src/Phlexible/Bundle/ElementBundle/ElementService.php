<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle;

use Phlexible\Bundle\ElementBundle\Element\Element;
use Phlexible\Bundle\ElementBundle\Element\ElementRepository;
use Phlexible\Bundle\ElementBundle\ElementStructure\ElementStructure;
use Phlexible\Bundle\ElementBundle\ElementStructure\ElementStructureLoader;
use Phlexible\Bundle\ElementBundle\ElementVersion\ElementVersion;
use Phlexible\Bundle\ElementBundle\ElementVersion\ElementVersionRepository;
use Phlexible\Bundle\ElementtypeBundle\Elementtype\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeService;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeVersion\ElementtypeVersion;

/**
 * Element service
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementService
{
    /**
     * @var ElementRepository
     */
    private $elementRepository;

    /**
     * @var ElementVersionRepository
     */
    private $elementVersionRepository;

    /**
     * @var ElementStructureLoader
     */
    private $elementVersionDataLoader;

    /**
     * @var ElementtypeService
     */
    private $elementtypeService;

    /**
     * @param ElementRepository        $elementRepository
     * @param ElementVersionRepository $elementVersionRepository
     * @param ElementStructureLoader   $elementStructureLoader
     * @param ElementtypeService       $elementtypeService
     */
    public function __construct(
        ElementRepository $elementRepository,
        ElementVersionRepository $elementVersionRepository,
        ElementStructureLoader $elementStructureLoader,
        ElementtypeService $elementtypeService)
    {
        $this->elementRepository = $elementRepository;
        $this->elementVersionRepository = $elementVersionRepository;
        $this->elementStructureLoader = $elementStructureLoader;
        $this->elementtypeService = $elementtypeService;
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
        return $this->elementRepository->find($eid);
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
        return $this->elementRepository->findByUniqueID($uniqueID);
    }

    /**
     * @param Element $element
     * @param int     $version
     *
     * @return ElementVersion
     */
    public function findElementVersion(Element $element, $version)
    {
        $elementVersion = $this->elementVersionRepository->find($element, $version);

        return $elementVersion;
    }

    /**
     * @param Element $element
     *
     * @return ElementVersion
     */
    public function findLatestElementVersion(Element $element)
    {
        $elementVersion = $this->elementVersionRepository->find($element, $element->getLatestVersion());

        return $elementVersion;
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
        return $this->elementVersionRepository->getVersions($element);
    }

    /**
     * @param ElementVersion $elementVersion
     * @param string         $language
     *
     * @return ElementStructure
     */
    public function findElementStructure(ElementVersion $elementVersion, $language)
    {
        $elementVersionData = $this->elementStructureLoader->load($elementVersion, $language);

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
}
