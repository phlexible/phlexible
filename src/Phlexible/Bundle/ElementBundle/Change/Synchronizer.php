<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Change;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\ElementBundle\Entity\ElementSource;
use Phlexible\Bundle\ElementBundle\Model\ElementManagerInterface;
use Phlexible\Bundle\ElementBundle\Model\ElementSourceManagerInterface;
use Phlexible\Bundle\ElementBundle\Model\ElementVersionManagerInterface;
use Phlexible\Bundle\ElementtypeBundle\File\Dumper\XmlDumper;
use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;
use Psr\Log\LoggerInterface;

/**
 * Synchronizer
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @TODO elementSourceManager
 */
class Synchronizer
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
     * @var ElementSourceManagerInterface
     */
    private $elementSourceManager;

    /**
     * @var XmlDumper
     */
    private $xmlDumper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ElementManagerInterface        $elementManager
     * @param ElementVersionManagerInterface $elementVersionManager
     * @param ElementSourceManagerInterface  $elementSourceManager
     * @param XmlDumper                      $xmlDumper
     * @param LoggerInterface                $logger
     */
    public function __construct(
        ElementManagerInterface $elementManager,
        ElementVersionManagerInterface $elementVersionManager,
        ElementSourceManagerInterface $elementSourceManager,
        XmlDumper $xmlDumper,
        LoggerInterface $logger
    )
    {
        $this->elementManager = $elementManager;
        $this->elementVersionManager = $elementVersionManager;
        $this->elementSourceManager = $elementSourceManager;
        $this->xmlDumper = $xmlDumper;
        $this->logger = $logger;
    }

    /**
     * @param Change $change
     */
    public function synchronize(Change $change)
    {
        $elementtype = $change->getElementtype();
        if ($change instanceof AddChange) {
            $this->logger->notice("Adding element source from elementtype {$change->getElementtype()->getUniqueId()}");
            $elementSource = new ElementSource();
            $this->applyElementtypeToElementSource($elementtype, $elementSource);
        } elseif ($change instanceof UpdateChange) {
            $this->logger->notice("Updating element source from elementtype {$change->getElementtype()->getUniqueId()}");
            $elementSource = new ElementSource();
            $this->applyElementtypeToElementSource($elementtype, $elementSource);
            $this->handleOutdatedElementSource($change, $elementSource);
        } elseif ($change instanceof RemoveChange) {
            $this->logger->notice("Removing obsolete element source {$change->getElementtype()->getUniqueId()}");
            $this->handleRemovedElementSource($change);
        } else {
            //$elementSource = $this->elementSourceManager->findOneByElementtypeAndRevision($elementtype);
        }
    }

    /**
     * @param UpdateChange  $change
     * @param ElementSource $elementSource
     */
    private function handleOutdatedElementSource(UpdateChange $change, ElementSource $elementSource)
    {
        foreach ($change->getOutdatedElementSources() as $outdatedElementSource) {
            $this->logger->info("Finding elements for outdated element source {$outdatedElementSource->getName()} {$outdatedElementSource->getElementtypeRevision()}");
            $elementVersions = $this->elementVersionManager->findByElementSource($outdatedElementSource);
            foreach ($elementVersions as $elementVersion) {
                $this->logger->info("Updating outdated element {$elementVersion->getId()} {$elementVersion->getVersion()}");
                $elementVersion->setElementSource($elementSource);
                $this->elementVersionManager->updateElementVersion($elementVersion, false);
            }

            $this->removeElementSource($outdatedElementSource);
        }
    }

    /**
     * @param RemoveChange $change
     */
    private function handleRemovedElementSource(RemoveChange $change)
    {
        foreach ($change->getRemovedElementSources() as $removedElementSource) {
            // remove elements
            $elements = $this->elementManager->findBy(array('elementtypeId' => $removedElementSource->getElementtypeId()));
            foreach ($elements as $element) {
                $this->logger->info("Removing element {$element->getEid()}");
                $this->elementManager->deleteElement($element);
            }

            $this->removeElementSource($removedElementSource);
        }
    }

    /**
     * @param Elementtype   $elementtype
     * @param ElementSource $elementSource
     */
    private function applyElementtypeToElementSource(Elementtype $elementtype, ElementSource $elementSource)
    {
        $elementSource
            ->setName($elementtype->getUniqueId())
            ->setElementtypeId($elementtype->getId())
            ->setElementtypeRevision($elementtype->getRevision())
            ->setType($elementtype->getType())
            ->setTemplate($elementtype->getTemplate() ?: null)
            ->setIcon($elementtype->getIcon() ?: null)
            ->setDefaultTab($elementtype->getDefaultTab())
            ->setDefaultContentTab($elementtype->getDefaultContentTab())
            ->setHideChildren($elementtype->getHideChildren())
            ->setNoIndex($elementtype->getNoIndex())
            ->setMetaSetId($elementtype->getMetaSetId() ?: null)
            ->setXml($this->xmlDumper->dump($elementtype))
            ->setImportedAt(new \DateTime());

        $this->elementSourceManager->updateElementSource($elementSource, false);
    }

    private function removeElementSource(ElementSource $elementSource)
    {
        $this->elementSourceManager->deleteElementSource($elementSource);
    }
}
