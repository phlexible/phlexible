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
use Phlexible\Bundle\ElementtypeBundle\ElementtypesMessage;
use Phlexible\Bundle\ElementtypeBundle\Entity\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\Entity\Repository\ElementtypeRepository;
use Phlexible\Bundle\ElementtypeBundle\Event\ElementtypeEvent;
use Phlexible\Bundle\ElementtypeBundle\Exception\CreateCancelledException;
use Phlexible\Bundle\ElementtypeBundle\Exception\DeleteCancelledException;
use Phlexible\Bundle\ElementtypeBundle\Exception\UpdateCancelledException;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeManagerInterface;
use Phlexible\Bundle\MessageBundle\Message\MessagePoster;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Elementtype manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeManager implements ElementtypeManagerInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var MessagePoster
     */
    private $messageService;

    /**
     * ElementtypeRepository
     */
    private $elementtypeRepository;

    /**
     * @param EntityManager            $entityManager
     * @param EventDispatcherInterface $dispatcher
     * @param MessagePoster            $messageService
     */
    public function __construct(
        EntityManager $entityManager,
        EventDispatcherInterface $dispatcher,
        MessagePoster $messageService)
    {
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
        $this->messageService = $messageService;
    }

    /**
     * @return ElementtypeRepository
     */
    private function getElementtypeRepository()
    {
        if (null === $this->elementtypeRepository) {
            $this->elementtypeRepository = $this->entityManager->getRepository('PhlexibleElementtypeBundle:Elementtype');
        }

        return $this->elementtypeRepository;
    }

    /**
     * Find element type by ID
     *
     * @param int $elementtypeId
     *
     * @return Elementtype
     */
    public function find($elementtypeId)
    {
        return $this->getElementtypeRepository()->find($elementtypeId);
    }

    /**
     * Find element type by unique Id
     *
     * @param string $uniqueId
     *
     * @return Elementtype
     */
    public function findOneByUniqueId($uniqueId)
    {
        return $this->getElementtypeRepository()->findOneBy(array('uniqueId' => $uniqueId));
    }

    /**
     * Find element types by type
     *
     * @param string $type
     *
     * @return Elementtype[]
     */
    public function findByType($type)
    {
        return $this->getElementtypeRepository()->findBy(array('type' => $type));
    }

    /**
     * Find all element types
     *
     * @return Elementtype[]
     */
    public function findAll()
    {
        return $this->getElementtypeRepository()->findAll();
    }

    /**
     * Save element type
     *
     * @param Elementtype $elementtype
     *
     * @throws CreateCancelledException
     * @throws UpdateCancelledException
     * @return $this
     */
    public function updateElementtype(Elementtype $elementtype, $flush = true)
    {
        if (!$this->entityManager->contains($elementtype)) {
            $event = new ElementtypeEvent($elementtype);
            if ($this->dispatcher->dispatch(ElementtypeEvents::BEFORE_CREATE, $event)->isPropagationStopped()) {
                throw new CreateCancelledException('Create canceled by callback.');
            }

            $this->entityManager->persist($elementtype);
            if ($flush) {
                $this->entityManager->flush($elementtype);
            }

            $event = new ElementtypeEvent($elementtype);
            $this->dispatcher->dispatch(ElementtypeEvents::CREATE, $event);

            // post message
            $message = ElementtypesMessage::create('Element Type "' . $elementtype->getId() . ' created.');
            $this->messageService->post($message);
        } else {
            $event = new ElementtypeEvent($elementtype);
            if ($this->dispatcher->dispatch(ElementtypeEvents::BEFORE_UPDATE, $event)->isPropagationStopped()) {
                throw new UpdateCancelledException('Create canceled by callback.');
            }

            $this->entityManager->persist($elementtype);
            if ($flush) {
                $this->entityManager->flush($elementtype);
            }

            $event = new ElementtypeEvent($elementtype);
            $this->dispatcher->dispatch(ElementtypeEvents::UPDATE, $event);

            // post message
            $message = ElementtypesMessage::create('Element Type "' . $elementtype->getId() . ' updated.');
            $this->messageService->post($message);
        }

        return $this;
    }

    /**
     * Delete an Element Type
     *
     * @param Elementtype $elementtype
     *
     * @throws DeleteCancelledException
     * @return $this
     */
    public function deleteElementtype(Elementtype $elementtype)
    {
        // post before event
        $event = new ElementtypeEvent($elementtype);
        if ($this->dispatcher->dispatch(ElementtypeEvents::BEFORE_DELETE, $event)->isPropagationStopped()) {
            throw new DeleteCancelledException('Delete canceled by listener.');
        }

        $delete = true;

        if ($elementtype->getType() == ElementtypeVersion::TYPE_REFERENCE) {
            $db = MWF_Registry::getContainer()->dbPool->default;
            $select = $db->select()
                ->distinct()
                ->from(
                    $db->prefix . 'elementtype_structure',
                    array('elementtype_id', new Zend_Db_Expr('MAX(version) AS max_version'))
                )
                ->where('reference_id = ?', $elementtype->getId())
                ->group('elementtype_id');

            $result = $db->fetchAll($select);

            if (count($result)) {
                $delete = false;

                $select = $db->select()
                    ->from($db->prefix . 'elementtype', 'latest_version')
                    ->where('elementtype_id = ?');

                foreach ($result as $row) {
                    $latestElementTypeVersion = $db->fetchOne($select, $row['elementtype_id']);

                    if ($latestElementTypeVersion == $row['max_version']) {
                        throw new ElementtypeException('Reference in use, can\'t delete.');
                    }
                }
            }
        }

        if ($delete) {
            $this->loader->delete($elementtype);

            // send message
            $message = ElementtypesMessage::create('Element type "' . $elementtype->getId() . '" deleted.');
            $this->messageService->post($message);
        } else {
            $this->loader->softDelete($elementtype);

            // send message
            $message = ElementtypesMessage::create('Element type "' . $elementtype->getId() . '" soft deleted.');
            $this->messageService->post($message);
        }

        // post event
        $event = new ElementtypeEvent($elementtype);
        $this->dispatcher->dispatch(ElementtypeEvents::DELETE, $event);

        return $this;
    }
}
