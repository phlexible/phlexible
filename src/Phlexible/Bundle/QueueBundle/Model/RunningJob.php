<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\QueueBundle\Model;

use Phlexible\Bundle\QueueBundle\Entity\Job;
use Symfony\Component\Process\Process;

/**
 * Running job
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RunningJob
{
    /**
     * @var Process
     */
    private $process;

    /**
     * @var Job
     */
    private $job;

    /**
     * @var int
     */
    private $outputPointer = 0;

    /**
     * @var int
     */
    private $errorOutputPointer = 0;

    /**
     * @var int
     */
    private $startTime;

    /**
     * @param Process $process
     * @param Job     $job
     */
    public function __construct(Process $process, Job $job)
    {
        $this->process = $process;
        $this->job = $job;
        $this->startTime = time();
    }

    /**
     * @return Process
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * @return Job
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @return int
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @return int
     */
    public function getOutputPointer()
    {
        return $this->outputPointer;
    }

    /**
     * @param int $outputPointer
     *
     * @return $this
     */
    public function setOutputPointer($outputPointer)
    {
        $this->outputPointer = $outputPointer;

        return $this;
    }

    /**
     * @return int
     */
    public function getErrorOutputPointer()
    {
        return $this->errorOutputPointer;
    }

    /**
     * @param int $errorOutputPointer
     *
     * @return $this
     */
    public function setErrorOutputPointer($errorOutputPointer)
    {
        $this->errorOutputPointer = $errorOutputPointer;

        return $this;
    }
}
