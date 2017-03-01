<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\QueueBundle\Model;

use Phlexible\Bundle\QueueBundle\Entity\Job;

/**
 * Job manager interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface JobManagerInterface
{
    /**
     * @param string $id
     *
     * @return Job
     */
    public function find($id);

    /**
     * @param array      $criteria
     * @param null|array $orderBy
     *
     * @return Job
     */
    public function findOneBy(array $criteria, array $orderBy = null);

    /**
     * @param array      $criteria
     * @param null|array $orderBy
     * @param null|int   $limit
     * @param null|int   $offset
     *
     * @return Job[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @return Job[]
     */
    public function findAll();

    /**
     * @return Job|null
     */
    public function findStartableJob();

    /**
     * @param array $criteria
     *
     * @return int
     */
    public function countBy(array $criteria);

    /**
     * Insert a new job into the queue.
     *
     * @param Job $job
     */
    public function addJob(Job $job);

    /**
     * Insert a new job into the queue, only if the same job doesn't exist in status waiting
     * Job ID and status will be set in the Job object.
     *
     * @param Job $job
     */
    public function addUniqueJob(Job $job);

    /**
     * Suspend a job (without removing it).
     *
     * @param Job $job
     */
    public function suspendJob(Job $job);

    /**
     * Resume a suspended job.
     *
     * @param Job $job
     */
    public function resumeJob(Job $job);

    /**
     * Requeue job.
     *
     * @param Job $job
     */
    public function requeueJob(Job $job);

    /**
     * @param Job $job
     *
     * @return Job
     */
    public function refreshJob(Job $job);

    /**
     * @param Job $job
     */
    public function updateJob(Job $job);

    /**
     * @param Job $job
     */
    public function deleteJob(Job $job);

    /**
     * @param string $state
     */
    public function deleteByState($state);
}
