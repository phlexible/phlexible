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
     * @param ElementtypeVersion $elementtypeVersion
     * @param string             $refererDsId
     *
     * @return ElementtypeStructure
     */
    public function find(ElementtypeVersion $elementtypeVersion, $refererDsId = null)
    {
        if (!isset($this->structureMap[$elementtypeVersion->getUniqueId()])) {
            $structure = new ElementtypeStructure();
            $structure
                ->setElementTypeVersion($elementtypeVersion);

            $this->doLoad(
                $structure,
                $elementtypeVersion->getElementtype()->getId(),
                $elementtypeVersion->getVersion(),
                $refererDsId
            );

            $this->structureMap[$elementtypeVersion->getUniqueId()] = $structure;
        }

        return $this->structureMap[$elementtypeVersion->getUniqueId()];
    }

    /**
     * @param ElementtypeStructure $elementtypeStructure
     *
     * @throws \Exception
     */
    public function updateElementtypeStructure(ElementtypeStructure $elementtypeStructure)
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

        $sort = 1;
        foreach ($rii as $node) {
            /* @var $node ElementtypeStructureNode */

            $data = array(
                'ds_id'               => $node->getDsId(),
                'elementtype_id'      => $elementtypeStructure->getElementtypeVersion()->getElementtype()->getId(),
                'elementtype_version' => $elementtypeStructure->getElementtypeVersion()->getVersion(),
                'parent_id'           => $node->getParentId(),
                'parent_ds_id'        => $node->getParentDsId(),
                'name'                => $node->getName() ? $node->getName() : '',
                'type'                => $node->getType(),
                'sort'                => $sort,
                'reference_id'        => $node->getReferenceId(),
                'reference_version'   => $node->getReferenceVersion(),
                'comment'             => $node->getComment(),
                'configuration'       => $node->getConfiguration() ? json_encode($node->getConfiguration()) : null,
                'validation'          => $node->getValidation() ? json_encode($node->getValidation()) : null,
                'labels'              => $node->getLabels() ? json_encode($node->getLabels()) : null,
                'options'             => $node->getOptions() ? json_encode($node->getOptions()) : null,
                'content_channels'    => $node->getContentChannels() ? json_encode($node->getContentChannels()) : null,
            );

            $this->db->insert($this->db->prefix . 'elementtype_structure', $data);

            $node->setId($this->db->lastInsertId());

            $sort++;
        }

        $event = new ElementtypeStructureEvent($elementtypeStructure);
        $this->dispatcher->dispatch(ElementtypeEvents::STRUCTURE_CREATE, $event);
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
            if ($referenceParentNode) {
                $node->setParentId($referenceParentNode->getId());
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
