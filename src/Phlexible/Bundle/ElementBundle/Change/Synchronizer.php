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
     * @param Change   $change
     * @param bool     $force
     * @param callable $callback
     */
    public function synchronize(Change $change, $force = false, callable $callback = null)
    {
        $elementtype = $change->getElementtype();
        $title = $elementtype->getTitle();
        $revision = $elementtype->getRevision();
        if ($change->getNeedImport()) {
            $elementSource = new ElementSource();
            $this->applyElementtypeToElementSource($elementtype, $elementSource);
        } else {
            $elementSource = $this->elementSourceManager->findOneByElementtypeAndRevision($elementtype);
        }

        if (!count($change->getOutdatedElementSources())) {
            $callback('noop', $title, $revision, 0, 0);

            return;
        }

        foreach ($change->getOutdatedElementSources() as $outdatedElementSource) {
            $elementVersions = $this->elementVersionManager->findByElementSource($outdatedElementSource);
            $total = count($elementVersions);
            $current = 0;
            if ($callback) {
                $callback('start', $title, $revision, $current, $total);
            }
            foreach ($elementVersions as $elementVersion) {
                $current++;
                if ($callback) {
                    $callback('progress', $title, $revision, $current, $total);
                }
                $elementVersion->setElementSource($elementSource);
                $this->elementVersionManager->updateElementVersion($elementVersion, true);
            }
            $this->removeOutdatedElementSource($outdatedElementSource);
            if ($callback) {
                $callback('end', $title, $revision, $current, $total);
            }
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
            ->setElementtype($elementtype)
            ->setImportedAt(new \DateTime());

        $this->elementSourceManager->updateElementSource($elementSource);
    }

    private function removeOutdatedElementSource(ElementSource $elementSource)
    {
        $this->elementSourceManager->deleteElementSource($elementSource);
    }
}
