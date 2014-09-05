<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\ElementBundle\ElementEvents;
use Phlexible\Bundle\ElementBundle\ElementsMessage;
use Phlexible\Bundle\ElementBundle\Entity\Element;
use Phlexible\Bundle\ElementBundle\Entity\Repository\ElementRepository;
use Phlexible\Bundle\ElementBundle\Event\ElementEvent;
use Phlexible\Bundle\ElementBundle\Model\ElementManagerInterface;
use Phlexible\Bundle\MessageBundle\Message\MessagePoster;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Element manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementManager implements ElementManagerInterface
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
     * @var MessagePoster
     */
    private $messagePoster;

    /**
     * @var ElementRepository
     */
    private $elementRepository;

    /**
     * @param EntityManager            $entityManager
     * @param EventDispatcherInterface $dispatcher
     * @param MessagePoster            $messagePoster
     */
    public function __construct(EntityManager $entityManager, EventDispatcherInterface $dispatcher, MessagePoster $messagePoster)
    {
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
        $this->messagePoster = $messagePoster;
    }

    /**
     * @return ElementRepository
     */
    private function getElementRepository()
    {
        if (null === $this->elementRepository) {
            $this->elementRepository = $this->entityManager->getRepository('PhlexibleElementBundle:Element');
        }

        return $this->elementRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->getElementRepository()->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getElementRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function updateElement(Element $element, $flush = true)
    {
        if (!$element->getEid()) {
            $event = new ElementEvent($element);
            if ($this->dispatcher->dispatch(ElementEvents::BEFORE_CREATE_ELEMENT, $event)->isPropagationStopped()) {
                throw new \Exception('Create canceled by listener.');
            }

            $this->entityManager->persist($element);

            if ($flush) {
                $this->entityManager->flush();
            }

            $event = new ElementEvent($element);
            $this->dispatcher->dispatch(ElementEvents::CREATE_ELEMENT, $event);

            // post message
            $message = ElementsMessage::create('Element "' . $element->getEid() . ' created.');
            $this->messagePoster->post($message);
        } else {
            $event = new ElementEvent($element);
            if ($this->dispatcher->dispatch(ElementEvents::BEFORE_UPDATE_ELEMENT, $event)->isPropagationStopped()) {
                throw new \Exception('Create canceled by listener.');
            }

            $this->entityManager->persist($element);
            if ($flush) {
                $this->entityManager->flush();
            }

            $event = new ElementEvent($element);
            $this->dispatcher->dispatch(ElementEvents::UPDATE_ELEMENT, $event);

            // post message
            $message = ElementsMessage::create('Element "' . $element->getEid() . ' updated.');
            $this->messagePoster->post($message);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteElement(Element $element)
    {
        // post before event
        $event = new ElementEvent($element);
        if ($this->dispatcher->dispatch(ElementEvents::BEFORE_DELETE_ELEMENT, $event)->isPropagationStopped()) {
            throw new \Exception('Delete canceled by listener.');
        }

        $delete = true;

        if ($elementtype->getType() == ElementtypeVersion::TYPE_REFERENCE) {
            $db = MWF_Registry::getContainer()->dbPool->default;
            $select = $db->select()
                ->distinct()
                ->from(
                    $db->prefix . 'elementtype_structure',
                    array('element_type_id', new Zend_Db_Expr('MAX(version) AS max_version'))
                )
                ->where('reference_id = ?', $elementtype->getId())
                ->group('element_type_id');

            $result = $db->fetchAll($select);

            if (count($result)) {
                $delete = false;

                $select = $db->select()
                    ->from($db->prefix . 'elementtype', 'latest_version')
                    ->where('element_type_id = ?');

                foreach ($result as $row) {
                    $latestElementTypeVersion = $db->fetchOne($select, $row['element_type_id']);

                    if ($latestElementTypeVersion == $row['max_version']) {
                        throw new \Exception('Reference in use, can\'t delete.');
                    }
                }
            }
        }

        if ($delete) {
            $this->loader->delete($elementtype);

            // send message
            $message = new ElementsMessage('Element type "' . $elementtype->getId() . '" deleted.');
            $message->post();
        } else {
            $this->loader->softDelete($elementtype);

            // send message
            $message = new ElementsMessage('Element type "' . $elementtype->getId() . '" soft deleted.');
            $message->post();
        }

        // post event
        $event = new DeleteEvent($elementtype);
        $this->dispatcher->dispatch($event);

        return $this;
    }
}
