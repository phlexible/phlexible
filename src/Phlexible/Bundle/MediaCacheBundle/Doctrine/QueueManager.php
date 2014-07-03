<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\MediaCacheBundle\Entity\QueueItem;
use Phlexible\Bundle\MediaCacheBundle\Entity\Repository\QueueItemRepository;
use Phlexible\Bundle\MediaCacheBundle\Model\QueueManagerInterface;

/**
 * Doctrine queue manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class QueueManager implements QueueManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var QueueItemRepository
     */
    private $queueRepository;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        $this->queueRepository = $entityManager->getRepository('PhlexibleMediaCacheBundle:QueueItem');
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->queueRepository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findByFile($fileId, $fileVersion = null)
    {
        $criteria = array('fileId' => $fileId);
        if ($fileVersion) {
            $criteria['fileVersion'] = $fileVersion;
        }

        return $this->queueRepository->findBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findByTemplateAndFile($templateKey, $fileId, $fileVersion)
    {
        return $this->queueRepository->findOneBy(array('template' => $templateKey, 'fileId' => $fileId, 'fileVersion' => $fileVersion));
    }

    /**
     * {@inheritdoc}
     */
    public function countAll()
    {
        return $this->queueRepository->countAll();
    }

    /**
     * {@inheritdoc}
     */
    public function updateQueueItem(QueueItem $queueItem)
    {
        $this->entityManager->persist($queueItem);
        $this->entityManager->flush($queueItem);
    }
}
