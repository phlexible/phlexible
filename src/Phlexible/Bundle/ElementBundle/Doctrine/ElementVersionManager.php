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
use Phlexible\Bundle\ElementBundle\Entity\ElementSource;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\ElementBundle\Entity\Repository\ElementVersionRepository;
use Phlexible\Bundle\ElementBundle\Event\ElementVersionEvent;
use Phlexible\Bundle\ElementBundle\Exception\CreateCancelledException;
use Phlexible\Bundle\ElementBundle\Exception\UpdateCancelledException;
use Phlexible\Bundle\ElementBundle\Model\ElementVersionManagerInterface;
use Phlexible\Bundle\MessageBundle\Message\MessagePoster;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Element version manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementVersionManager implements ElementVersionManagerInterface
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
     * @var ElementVersionRepository
     */
    private $elementVersionRepository;

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
     * @return ElementVersionRepository
     */
    private function getElementVersionRepository()
    {
        if (null === $this->elementVersionRepository) {
            $this->elementVersionRepository = $this->entityManager->getRepository('PhlexibleElementBundle:ElementVersion');
        }

        return $this->elementVersionRepository;
    }

    /**
     * @var ElementVersion[]
     */
    private $elementVersions = [];

    /**
     * {@inheritdoc}
     */
    public function find(Element $element, $version)
    {
        $index = $element->getEid() . '__' . $version;

        if (!isset($this->elementVersions[$index]) || $this->elementVersions[$index] === null) {
            $elementVersion = $this->getElementVersionRepository()->findOneBy(
                [
                    'element' => $element,
                    'version' => $version,
                ]
            );
            $this->elementVersions[$index] = $elementVersion;
        }

        return $this->elementVersions[$index];
    }

    /**
     * {@inheritdoc}
     */
    public function findByElementSource(ElementSource $elementSource)
    {
        return $this->getElementVersionRepository()->findBy(['elementSource' => $elementSource]);
    }

    /**
     * {@inheritdoc}
     */
    public function getVersions(Element $element)
    {
        return $this->getElementVersionRepository()->getVersions($element);
    }

    /**
     * {@inheritdoc}
     */
    public function updateElementVersion(ElementVersion $elementVersion, $flush = true)
    {
        if (!$elementVersion->getId()) {
            $event = new ElementVersionEvent($elementVersion);
            if ($this->dispatcher->dispatch(ElementEvents::BEFORE_CREATE_ELEMENT_VERSION, $event)->isPropagationStopped()) {
                throw new CreateCancelledException('Create canceled by listener.');
            }

            $this->entityManager->persist($elementVersion);
            foreach ($elementVersion->getMappedFields() as $mappedField) {
                $this->entityManager->persist($mappedField);
            }

            if ($flush) {
                $this->entityManager->flush();
            }

            $event = new ElementVersionEvent($elementVersion);
            $this->dispatcher->dispatch(ElementEvents::CREATE_ELEMENT_VERSION, $event);

            // post message
            $message = ElementsMessage::create('Element version "' . $elementVersion->getElement()->getEid() . ' updated.');
            $this->messagePoster->post($message);
        } else {
            $event = new ElementVersionEvent($elementVersion);
            if ($this->dispatcher->dispatch(ElementEvents::BEFORE_UPDATE_ELEMENT_VERSION, $event)->isPropagationStopped()) {
                throw new UpdateCancelledException('Update canceled by listener.');
            }

            foreach ($elementVersion->getMappedFields() as $mappedField) {
                $this->entityManager->persist($mappedField);
            }

            if ($flush) {
                $this->entityManager->flush();
            }

            $event = new ElementVersionEvent($elementVersion);
            $this->dispatcher->dispatch(ElementEvents::UPDATE_ELEMENT_VERSION, $event);

            // post message
            $message = ElementsMessage::create('Element version "' . $elementVersion->getElement()->getEid() . ' updated.');
            $this->messagePoster->post($message);
        }
    }
}
