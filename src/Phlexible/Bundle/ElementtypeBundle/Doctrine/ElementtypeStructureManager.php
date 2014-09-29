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
use Phlexible\Bundle\ElementtypeBundle\Entity\ElementtypeVersion;
use Phlexible\Bundle\ElementtypeBundle\Entity\Repository\ElementtypeStructureNodeRepository;
use Phlexible\Bundle\ElementtypeBundle\Event\ElementtypeStructureEvent;
use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructure;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructureManagerInterface;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructureNode;
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
        $identifier = $elementtypeVersion->getElementtype()->getId().'_'.$elementtypeVersion->getVersion();

        if (!isset($this->structureMap[$identifier])) {
            $structure = new ElementtypeStructure();
            $structure
                ->setElementtypeVersion($elementtypeVersion);

            $this->doLoad(
                $structure,
                $elementtypeVersion->getElementtype()->getId(),
                $elementtypeVersion->getVersion()
            );

            $this->structureMap[$identifier] = $structure;
        }

        return $this->structureMap[$identifier];
    }

    /**
     * @param \Phlexible\Bundle\ElementtypeBundle\Model\Elementtype $referenceElementtype
     *
     * @return ElementtypeStructureNode[]
     */
    public function findElementtypesUsingReferenceElementtype(Elementtype $referenceElementtype)
    {
        $conn = $this->entityManager->getConnection();
        $qb = $conn->createQueryBuilder();
        $qb
            ->select('ets.elementtype_id')
            ->from('elementtype_structure', 'ets')
            ->join('ets', 'elementtype', 'e', 'ets.elementtype_version = e.latest_version')
            ->where($qb->expr()->eq('ets.reference_id', $referenceElementtype->getId()));

        $ids = $conn->fetchAll($qb->getSQL());
        $ids = array_column($ids, 'elementtype_id');

        return $this->entityManager->getRepository('PhlexibleElementtypeBundle:Elementtype')->findBy(array('id' => $ids));
    }

    /**
     * {@inheritdoc}
     */
    public function updateElementtypeStructure(ElementtypeStructure $elementtypeStructure, $flush = true)
    {
        $event = new ElementtypeStructureEvent($elementtypeStructure);
        if ($this->dispatcher->dispatch(ElementtypeEvents::BEFORE_STRUCTURE_CREATE, $event)->isPropagationStopped()) {
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
            /* @var $node \Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructureNode */

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

            if (!$node->isReferenced()) {
                $this->entityManager->remove($node);
            }
        }

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param ElementtypeStructure     $structure
     * @param int                      $id
     * @param int                      $version
     * @param \Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructureNode $referenceParentNode
     */
    private function doLoad(ElementtypeStructure $structure, $id, $version, $referenceParentNode = null)
    {
        $nodes = $this->getStructureNodeRepository()->findBy(
            array(
                'elementtypeId' => $id,
                'version' => $version
            )
        );

        $isReferenced = false;
        if ($referenceParentNode) {
            $isReferenced = true;
        }

        foreach ($nodes as $node) {
            /* @var $node ElementtypeStructureNode */

            if ($isReferenced) {
                $node = clone $node;

                if ($referenceParentNode) {
                    $node->setParentNode($referenceParentNode);
                    $node->setParentDsId($referenceParentNode->getDsId());
                    $referenceParentNode = null;
                }

                $node->setReferenced(true);
            }

            $structure->addNode($node);

            if ($node->isReference()) {
                $this->doLoad($structure, $node->getReferenceElementtype()->getId(), $node->getReferenceVersion(), $node);
            }
        }
    }
}
