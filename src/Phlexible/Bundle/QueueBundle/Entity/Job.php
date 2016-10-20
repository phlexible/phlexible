<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\QueueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Job
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity(repositoryClass = "Phlexible\Bundle\QueueBundle\Entity\Repository\JobRepository")
 * @ORM\Table(name="job")
 */
class Job
{
    const STATE_PENDING = 'pending';
    const STATE_RUNNING = 'running';
    const STATE_FINISHED = 'finished';
    const STATE_FAILED = 'failed';
    const STATE_SUSPENDED = 'suspended';
    const STATE_ABORTED = 'aborted';

    const PRIORITY_LOW = -5;
    const PRIORITY_DEFAULT = 0;
    const PRIORITY_HIGH = 5;

    /**
     * @var string
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string", length=36, options={"fixed" = true})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $command;

    /**
     * @var string
     * @ORM\Column(type="json_array")
     */
    private $arguments = [];

    /**
     * @var string
     * @ORM\Column(type="string", length=20, options={"default"="pending"})
     */
    private $state = self::STATE_PENDING;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $priority = 0;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $maxRuntime = 0;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="execute_after", type="datetime")
     */
    private $executeAfter;

    /**
     * @var \DateTime
     * @ORM\Column(name="started_at", type="datetime", nullable=true)
     */
    private $startedAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="finished_at", type="datetime", nullable=true)
     */
    private $finishedAt;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $output;

    /**
     * @var string
     * @ORM\Column(name="error_output", type="text", nullable=true)
     */
    private $errorOutput;

    /**
     * @var string
     * @ORM\Column(name="stack_trace", type="object", nullable=true)
     */
    private $stackTrace;

    /**
     * @var int
     * @ORM\Column(name="run_time", type="integer", nullable=true)
     */
    private $runtime;

    /**
     * @var int
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $memoryUsage;

    /**
     * @var int
     * @ORM\Column(name="exit_code", type="integer", nullable=true)
     */
    private $exitCode;

    /**
     * @param string $command
     * @param array  $arguments
     */
    public function __construct($command, array $arguments = [])
    {
        $this->setCommand($command);
        $this->setArguments($arguments);

        $this->setCreatedAt(new \DateTime());
        $this->setExecuteAfter(new \DateTime());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            'Job(id = %s, command = "%s", arguments="%s")',
            $this->id,
            $this->command,
            implode(' ', $this->arguments)
        );
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = (string) $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param string $command
     *
     * @return $this
     */
    public function setCommand($command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param array $arguments
     *
     * @return $this
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;

        return $this;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     *
     * @return $this
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     *
     * @return $this
     */
    public function setPriority($priority)
    {
        $this->priority = (int) $priority;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaxRuntime()
    {
        return $this->maxRuntime;
    }

    /**
     * @param mixed $maxRuntime
     *
     * @return $this
     */
    public function setMaxRuntime($maxRuntime)
    {
        $this->maxRuntime = $maxRuntime;

        return $this;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $executeAt
     *
     * @return $this
     */
    public function setExecuteAfter(\DateTime $executeAt)
    {
        $this->executeAfter = $executeAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getExecuteAfter()
    {
        return $this->executeAfter;
    }

    /**
     * @param \DateTime $startedAt
     *
     * @return $this
     */
    public function setStartedAt(\DateTime $startedAt)
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * @param \DateTime $finishedAt
     *
     * @return $this
     */
    public function setFinishedAt(\DateTime $finishedAt)
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getFinishedAt()
    {
        return $this->finishedAt;
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param string $output
     *
     * @return $this
     */
    public function setOutput($output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * @param string $output
     *
     * @return $this
     */
    public function addOutput($output)
    {
        $this->output .= $output;

        return $this;
    }

    /**
     * @return string
     */
    public function getErrorOutput()
    {
        return $this->errorOutput;
    }

    /**
     * @param string $errorOutput
     *
     * @return $this
     */
    public function setErrorOutput($errorOutput)
    {
        $this->errorOutput = $errorOutput;

        return $this;
    }

    /**
     * @param string $errorOutput
     *
     * @return $this
     */
    public function addErrorOutput($errorOutput)
    {
        $this->errorOutput .= $errorOutput;

        return $this;
    }

    /**
     * @return string
     */
    public function getStackTrace()
    {
        return $this->stackTrace;
    }

    /**
     * @param string $stackTrace
     *
     * @return $this
     */
    public function setStackTrace($stackTrace)
    {
        $this->stackTrace = $stackTrace;

        return $this;
    }

    /**
     * @return int
     */
    public function getRuntime()
    {
        return $this->runtime;
    }

    /**
     * @param int $runtime
     *
     * @return $this
     */
    public function setRuntime($runtime)
    {
        $this->runtime = $runtime;

        return $this;
    }

    /**
     * @return int
     */
    public function getMemoryUsage()
    {
        return $this->memoryUsage;
    }

    /**
     * @param int $memoryUsage
     *
     * @return $this
     */
    public function setMemoryUsage($memoryUsage)
    {
        $this->memoryUsage = $memoryUsage;

        return $this;
    }

    /**
     * @return int
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }

    /**
     * @param int $exitCode
     *
     * @return $this
     */
    public function setExitCode($exitCode)
    {
        $this->exitCode = $exitCode;

        return $this;
    }
}
