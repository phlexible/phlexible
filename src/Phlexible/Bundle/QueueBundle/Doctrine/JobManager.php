<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\QueueBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\QueueBundle\Entity\Job;
use Phlexible\Bundle\QueueBundle\Entity\Repository\JobRepository;
use Phlexible\Bundle\QueueBundle\Model\JobManagerInterface;

/**
 * Job manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class JobManager implements JobManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var JobRepository
     */
    private $jobRepository;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository|JobRepository
     */
    private function getJobRepository()
    {
        if (null === $this->jobRepository) {
            $this->jobRepository = $this->entityManager->getRepository('PhlexibleQueueBundle:Job');
        }

        return $this->jobRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->getJobRepository()->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return $this->getJobRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getJobRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->getJobRepository()->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function findStartableJob()
    {
        $qb = $this->getJobRepository()->createQueryBuilder('j');
        $qb
            ->where($qb->expr()->eq('j.state', $qb->expr()->literal(Job::STATE_PENDING)))
            ->andWhere($qb->expr()->lte('j.executeAfter', $qb->expr()->literal(date('Y-m-d H:i:s'))))
            ->orderBy('j.priority', 'DESC')
            ->orderBy('j.createdAt', 'ASC')
            ->setMaxResults(1);

        $result = $qb->getQuery()->getResult();
        if (count($result)) {
            return current($result);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function countBy(array $criteria)
    {
        $qb = $this->getJobRepository()->createQueryBuilder('j');
        $qb->select('COUNT(j.id)');
        foreach ($criteria as $field => $value) {
            $qb->andWhere($qb->expr()->eq("j.$field", $qb->expr()->literal($value)));
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function addJob(Job $job)
    {
        $this->updateJob($job);
    }

    /**
     * {@inheritdoc}
     */
    public function addUniqueJob(Job $job)
    {
        $queueItem = $this->getJobRepository()->findBy(
            array('command' => $job->getCommand(), 'arguments' => $job->getArguments())
        );

        if ($queueItem) {
            return;
        }

        $this->addJob($job);
    }

    /**
     * {@inheritdoc}
     */
    public function suspendJob(Job $job)
    {
        $job->setState(Job::STATE_SUSPENDED);
        $this->updateJob($job);
    }

    /**
     * {@inheritdoc}
     */
    public function resumeJob(Job $job)
    {
        if ($job->getState() === Job::STATE_SUSPENDED) {
            $job->setState(Job::STATE_PENDING);
            $this->updateJob($job);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function requeueJob(Job $job)
    {
        $job->setState(Job::STATE_PENDING);
        $this->updateJob($job);
    }

    /**
     * {@inheritdoc}
     */
    public function refreshJob(Job $job)
    {
        $this->entityManager->refresh($job);

        return $job;
    }

    /**
     * {@inheritdoc}
     */
    public function updateJob(Job $job)
    {
        $this->entityManager->persist($job);
        $this->entityManager->flush($job);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteJob(Job $job)
    {
        $this->entityManager->remove($job);

        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByState($state)
    {
        $qb = $this->getJobRepository()->createQueryBuilder('j');
        $qb
            ->delete('PhlexibleQueueBundle:Job', 'j')
            ->where($qb->expr()->eq('j.state', $qb->expr()->literal($state)));

        $qb->getQuery()->execute();
    }
}
