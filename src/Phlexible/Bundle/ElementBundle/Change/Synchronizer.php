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
use Phlexible\Bundle\ElementBundle\Model\ElementVersionManagerInterface;
use Phlexible\Bundle\ElementtypeBundle\File\Dumper\XmlDumper;
use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;

/**
 * Synchronizer
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * TODO: elementSourceManager
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
     * @var XmlDumper
     */
    private $xmlDumper;

    /**
     * @param EntityManager                  $entityManager
     * @param ElementVersionManagerInterface $elementVersionManager
     * @param XmlDumper                      $xmlDumper
     */
    public function __construct(EntityManager $entityManager, ElementVersionManagerInterface $elementVersionManager, XmlDumper $xmlDumper)
    {
        $this->entityManager = $entityManager;
        $this->elementVersionManager = $elementVersionManager;
        $this->xmlDumper = $xmlDumper;
    }

    /**
     * @param Change $change
     */
    public function synchronize(Change $change)
    {
        $this->importElementtype($change->getElementtype());

        $elementVersions = $change->getElementVersions();
        while(count($elementVersions)) {
            $elementVersion = array_shift($elementVersions);
            $elementVersion->setElementtypeVersion($change->getElementtype()->getRevision());
            $this->elementVersionManager->updateElementVersion($elementVersion, !count($elementVersions));
        }
    }

    /**
     * @param Elementtype $elementtype
     *
     * @return ElementSource
     */
    private function importElementtype(Elementtype $elementtype)
    {
        $elementSourceRepository = $this->entityManager->getRepository('PhlexibleElementBundle:ElementSource');

        $elementSource = $elementSourceRepository->findBy(array('id' => $elementtype->getId(), 'revision' => $elementtype->getRevision()));
        if ($elementSource) {
            return $elementSource;
        }

        $elementSource = new ElementSource();
        $elementSource
            ->setId($elementtype->getId())
            ->setCreatedAt(new \DateTime())
            ->setRevision($elementtype->getRevision())
            ->setXml($this->xmlDumper->dump($elementtype));

        $this->entityManager->persist($elementSource);
        $this->entityManager->flush($elementSource);

        return $elementSource;
    }
}
