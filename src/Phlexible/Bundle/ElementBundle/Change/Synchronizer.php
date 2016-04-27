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
use Phlexible\Bundle\ElementBundle\Model\ElementSourceManagerInterface;
use Phlexible\Bundle\ElementBundle\Model\ElementVersionManagerInterface;
use Phlexible\Bundle\ElementtypeBundle\File\Dumper\XmlDumper;
use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;

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
     * @var EntityManager
     */
    private $entityManager;

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
     * @param ElementVersionManagerInterface $elementVersionManager
     * @param ElementSourceManagerInterface  $elementSourceManager
     * @param XmlDumper                      $xmlDumper
     */
    public function __construct(
        ElementVersionManagerInterface $elementVersionManager,
        ElementSourceManagerInterface $elementSourceManager,
        XmlDumper $xmlDumper
    )
    {
        $this->elementSourceManager = $elementSourceManager;
        $this->elementVersionManager = $elementVersionManager;
        $this->xmlDumper = $xmlDumper;
    }

    /**
     * @param Change $change
     * @param bool   $force
     */
    public function synchronize(Change $change, $force = false)
    {
        $elementtype = $change->getElementtype();
        if ($change->getNeedImport()) {
            $elementSource = new ElementSource();
            $this->applyElementtypeToElementSource($elementtype, $elementSource);
        } else {
            $elementSource = $this->elementSourceManager->findOneByElementtypeAndRevision($elementtype);
        }

        foreach ($change->getOutdatedElementSources() as $outdatedElementSource) {
            $elementVersions = $this->elementVersionManager->findByElementSource($outdatedElementSource);
            foreach ($elementVersions as $elementVersion) {
                $elementVersion->setElementSource($elementSource);
                echo $elementVersion->getId()." ".$elementVersion->getVersion().PHP_EOL;
                $this->elementVersionManager->updateElementVersion($elementVersion, false);
            }
            $this->removeOutdatedElementSource($outdatedElementSource);
        }
    }

    /**
     * @param Elementtype   $elementtype
     * @param ElementSource $elementSource
     */
    private function applyElementtypeToElementSource(Elementtype $elementtype, ElementSource $elementSource)
    {
        $elementSource
            ->setElementtypeId($elementtype->getId())
            ->setElementtypeRevision($elementtype->getRevision())
            ->setType($elementtype->getType())
            ->setXml($this->xmlDumper->dump($elementtype))
            ->setImportedAt(new \DateTime());

        $this->elementSourceManager->updateElementSource($elementSource);
    }

    private function removeOutdatedElementSource(ElementSource $elementSource)
    {
        $this->elementSourceManager->deleteElementSource($elementSource);
    }
}
