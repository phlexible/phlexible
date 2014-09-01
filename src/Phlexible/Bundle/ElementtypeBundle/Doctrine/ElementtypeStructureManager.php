<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeEvents;
use Phlexible\Bundle\ElementtypeBundle\Entity\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\Entity\ElementtypeStructureNode;
use Phlexible\Bundle\ElementtypeBundle\Entity\ElementtypeVersion;
use Phlexible\Bundle\ElementtypeBundle\Entity\Repository\ElementtypeStructureNodeRepository;
use Phlexible\Bundle\ElementtypeBundle\Event\ElementtypeStructureEvent;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructure;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructureManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Element structure manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeStructureManager implements ElementtypeStructureManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var ElementtypeStructureNodeRepository
     */
    private $structureNodeRepository;

    /**
     * @param EntityManager            $entityManager
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EntityManager $entityManager, EventDispatcherInterface $dispatcher)
    {
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return ElementtypeStructureNodeRepository
     */
    private function getStructureNodeRepository()
    {
        if (null === $this->structureNodeRepository) {
            $this->structureNodeRepository = $this->entityManager->getRepository('PhlexibleElementtypeBundle:ElementtypeStructureNode');
        }

        return $this->structureNodeRepository;
    }

    /**
     * @var ElementtypeStructure[]
     */
    private $structureMap = array();

    /**
     * {@inheritdoc}
     */
    public function find(ElementtypeVersion $elementtypeVersion)
    {
        if (!isset($this->structureMap[$elementtypeVersion->getUniqueId()])) {
            $structure = new ElementtypeStructure();
            $structure
                ->setElementtypeVersion($elementtypeVersion);

            $this->doLoad(
                $structure,
                $elementtypeVersion->getElementtype()->getId(),
                $elementtypeVersion->getVersion()
            );

            $this->structureMap[$elementtypeVersion->getUniqueId()] = $structure;
        }

        return $this->structureMap[$elementtypeVersion->getUniqueId()];
    }

    /**
     * @param Elementtype $referenceElementtype
     *
     * @return ElementtypeStructureNode[]
     */
    public function findNodesByReferenceElementtype(Elementtype $referenceElementtype)
    {
        $nodes = $this->getStructureNodeRepository()->findBy(
            array(
                'referenceElementtype' => $referenceElementtype,
                'referenceVersion'     => $referenceElementtype->getLatestVersion()
            )
        );

        return $nodes;
    }

    /**
     * @param ElementtypeVersion $referenceElementtypeVersion
     *
     * @return ElementtypeStructureNode[]
     */
    public function findNodesByReferenceElementtypeVersion(ElementtypeVersion $referenceElementtypeVersion)
    {
        $nodes = $this->getStructureNodeRepository()->findBy(
            array(
                'referenceElementtype' => $referenceElementtypeVersion->getElementtype(),
                'referenceVersion'     => $referenceElementtypeVersion->getVersion()
            )
        );

        return $nodes;
    }

    /**
     * {@inheritdoc}
     */
    public function updateElementtypeStructure(ElementtypeStructure $elementtypeStructure, $flush = true)
    {
        $event = new ElementtypeStructureEvent($elementtypeStructure);
        if (!$this->dispatcher->dispatch(ElementtypeEvents::BEFORE_STRUCTURE_CREATE, $event)) {
            throw new \Exception('Canceled by listener.');
        }

        /*
        if (!$this->getParentId() && $this->getParentDsId()) {
            $msg = 'Disambiguous parent information';
            throw new \Exception($msg);
        }
        */

        $rii = new \RecursiveIteratorIterator($elementtypeStructure->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);

        foreach ($rii as $node) {
            /* @var $node ElementtypeStructureNode */

            $this->entityManager->persist($node);
        }

        if ($flush) {
            $this->entityManager->flush();
        }

        $event = new ElementtypeStructureEvent($elementtypeStructure);
        $this->dispatcher->dispatch(ElementtypeEvents::STRUCTURE_CREATE, $event);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteElementtypeStructure(ElementtypeStructure $elementtypeStructure, $flush = true)
    {
        if (!$elementtypeStructure->getRootNode()) {
            return;
        }

        $rii = new \RecursiveIteratorIterator($elementtypeStructure->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);

        foreach ($rii as $node) {
            /* @var $node ElementtypeStructureNode */

            $this->entityManager->remove($node);
        }

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param ElementtypeStructure     $structure
     * @param int                      $id
     * @param int                      $version
     * @param ElementtypeStructureNode $referenceParentNode
     */
    private function doLoad(ElementtypeStructure $structure, $id, $version, $referenceParentNode = null)
    {
        $nodes = $this->getStructureNodeRepository()->findBy(
            array(
                'elementtype' => $id,
                'version' => $version
            )
        );

        foreach ($nodes as $node) {
            /* @var $node ElementtypeStructureNode */

            $node = clone $node;

            if ($referenceParentNode) {
                $node->setParentNode($referenceParentNode);
                $node->setParentDsId($referenceParentNode->getDsId());
                $referenceParentNode = null;
            }

            $structure->addNode($node);

            if ($node->isReference()) {
                $this->doLoad($structure, $node->getReferenceElementtype()->getId(), $node->getReferenceVersion(), $node);
            }
        }
    }
}
