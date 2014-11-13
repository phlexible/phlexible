<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\ElementBundle\ElementEvents;
use Phlexible\Bundle\ElementBundle\ElementsMessage;
use Phlexible\Bundle\ElementBundle\Entity\ElementSource;
use Phlexible\Bundle\ElementBundle\Event\ElementSourceEvent;
use Phlexible\Bundle\ElementBundle\Exception\CreateCancelledException;
use Phlexible\Bundle\ElementBundle\Exception\UpdateCancelledException;
use Phlexible\Bundle\ElementBundle\Model\ElementSourceManagerInterface;
use Phlexible\Bundle\ElementtypeBundle\File\Parser\XmlParser;
use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;
use Phlexible\Bundle\MessageBundle\Message\MessagePoster;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Element source manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementSourceManager implements ElementSourceManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var XmlParser
     */
    private $parser;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var MessagePoster
     */
    private $messagePoster;

    /**
     * @var EntityRepository
     */
    private $elementSourceRepository;

    /**
     * @param EntityManager $entityManager
     * @param EventDispatcherInterface $dispatcher
     * @param MessagePoster $messagePoster
     */
    public function __construct(
        EntityManager $entityManager,
        EventDispatcherInterface $dispatcher,
        MessagePoster $messagePoster
    )
    {
        $this->entityManager = $entityManager;
        $this->parser = new XmlParser();
        $this->dispatcher = $dispatcher;
        $this->messagePoster = $messagePoster;
    }

    /**
     * @return EntityRepository
     */
    private function getElementSourceRepository()
    {
        if (null === $this->elementSourceRepository) {
            $this->elementSourceRepository = $this->entityManager->getRepository(
                'PhlexibleElementBundle:ElementSource'
            );
        }

        return $this->elementSourceRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function findElementSource($elementtypeId)
    {
        return $this->getElementSourceRepository()->findOneBy(
            ['elementtypeId' => $elementtypeId],
            ['elementtypeRevision' => 'DESC']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function findByType($type)
    {
        return $this->getElementSourceRepository()->findBy(['type' => $type]);
    }

    /**
     * {@inheritdoc}
     */
    public function findElementtype($elementtypeId)
    {
        $elementSource = $this->findElementSource($elementtypeId);

        if (!$elementSource) {
            return null;
        }

        return $this->parser->parseString($elementSource->getXml());
    }

    /**
     * {@inheritdoc}
     */
    public function findElementtypesByType($type)
    {
        $elementtypes = [];
        foreach ($this->getElementSourceRepository()->findBy(['type' => $type]) as $elementSource) {
            $elementtypes[] = $this->findElementtypeByElementSource($elementSource);
        }

        return $elementtypes;
    }

    /**
     * {@inheritdoc}
     */
    public function findElementtypeByElementSource(ElementSource $elementSource)
    {
        return $this->parser->parseString($elementSource->getXml());
    }

    /**
     * {@inheritdoc}
     */
    public function findOutdatedElementSources(Elementtype $elementtype)
    {
        $qb = $this->getElementSourceRepository()->createQueryBuilder('es');
        $qb
            ->where($qb->expr()->eq('es.elementtypeId', $qb->expr()->literal($elementtype->getId())))
            ->andWhere($qb->expr()->neq('es.elementtypeRevision', $elementtype->getRevision()));

        return $qb->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findByElementtype(Elementtype $elementtype)
    {
        return $this->getElementSourceRepository()->findOneBy(
            [
                'elementtypeId'       => $elementtype->getId(),
                'elementtypeRevision' => $elementtype->getRevision()
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function updateElementSource(ElementSource $elementSource, $flush = true)
    {
        if (!$elementSource->getId()) {
            $event = new ElementSourceEvent($elementSource);
            if ($this->dispatcher->dispatch(ElementEvents::BEFORE_CREATE_ELEMENT_SOURCE, $event)->isPropagationStopped()) {
                throw new CreateCancelledException('Create canceled by listener.');
            }

            $this->entityManager->persist($elementSource);

            if ($flush) {
                $this->entityManager->flush();
            }

            $event = new ElementSourceEvent($elementSource);
            $this->dispatcher->dispatch(ElementEvents::CREATE_ELEMENT_SOURCE, $event);

            // post message
            $message = ElementsMessage::create('Element source "' . $elementSource->getId() . ' created.');
            $this->messagePoster->post($message);
        } else {
            $event = new ElementSourceEvent($elementSource);
            if ($this->dispatcher->dispatch(ElementEvents::BEFORE_UPDATE_ELEMENT_SOURCE, $event)->isPropagationStopped()) {
                throw new UpdateCancelledException('Update canceled by listener.');
            }

            if ($flush) {
                $this->entityManager->flush();
            }

            $event = new ElementSourceEvent($elementSource);
            $this->dispatcher->dispatch(ElementEvents::UPDATE_ELEMENT_SOURCE, $event);

            // post message
            $message = ElementsMessage::create('Element source "' . $elementSource->getId() . ' updated.');
            $this->messagePoster->post($message);
        }
    }

    /**
     * @param ElementSource $elementSource
     */
    public function deleteElementSource(ElementSource $elementSource)
    {
        $this->entityManager->remove($elementSource);
        $this->entityManager->flush();
    }
}
