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
use Phlexible\Bundle\ElementtypeBundle\Entity\ElementtypeVersion;
use Phlexible\Bundle\ElementtypeBundle\Entity\Repository\ElementtypeVersionRepository;
use Phlexible\Bundle\ElementtypeBundle\Event\ElementtypeVersionEvent;
use Phlexible\Bundle\ElementtypeBundle\Exception\UpdateCancelledException;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeVersionManagerInterface;
use Phlexible\Bundle\MessageBundle\Message\MessagePoster;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Elementtype version repository
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeVersionManager implements ElementtypeVersionManagerInterface
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
    private $messageService;

    /**
     * @var ElementtypeVersionRepository
     */
    private $elementtypeVersionRepository;

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
     * @return ElementtypeVersionRepository
     */
    private function getElementtypeVersionRepository()
    {
        if (null === $this->elementtypeVersionRepository) {
            $this->elementtypeVersionRepository = $this->entityManager->getRepository('PhlexibleElementtypeBundle:ElementtypeVersion');
        }

        return $this->elementtypeVersionRepository;
    }

    /**
     * @param Elementtype $elementtype
     * @param int         $version
     *
     * @return ElementtypeVersion
     */
    public function find(Elementtype $elementtype, $version = null)
    {
        if (null === $version) {
            $version = $elementtype->getLatestVersion();
        }

        return $this->getElementtypeVersionRepository()->findOneBy(array('elementtype' => $elementtype, 'version' => $version));
    }

    /**
     * @param Elementtype $elementtype
     *
     * @return array
     */
    public function getVersions(Elementtype $elementtype)
    {
        $qb = $this->getElementtypeVersionRepository()->createQueryBuilder('etv');
        $qb
            ->select('etv.version')
            ->where($qb->expr()->eq('etv.elementtype', $elementtype->getId()))
            ->orderBy('etv.version', 'ASC');

        return array_column($qb->getQuery()->getScalarResult(), 'version');
    }

    /**
     * @param ElementtypeVersion $elementtypeVersion
     * @param bool               $flush
     *
     * @throws UpdateCancelledException
     */
    public function updateElementtypeVersion(ElementtypeVersion $elementtypeVersion, $flush = true)
    {
        $event = new ElementtypeVersionEvent($elementtypeVersion);
        if ($this->dispatcher->dispatch(ElementtypeEvents::BEFORE_VERSION_CREATE, $event)->isPropagationStopped()) {
            throw new UpdateCancelledException('Canceled by listener.');
        }

        $this->entityManager->persist($elementtypeVersion);
        if ($flush) {
            $this->entityManager->flush($elementtypeVersion);
        }

        $event = new ElementtypeVersionEvent($elementtypeVersion);
        $this->dispatcher->dispatch(ElementtypeEvents::VERSION_CREATE, $event);

        // post message
        $message = ElementtypesMessage::create('Element Type Version ' . $elementtypeVersion->getVersion() . ' updated.');
        $this->messageService->post($message);
    }
}
