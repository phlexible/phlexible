<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaCache\Worker;

use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Phlexible\Component\MediaType\Model\MediaType;

/**
 * Cache worker resolver.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class WorkerResolver
{
    /**
     * @var WorkerInterface[]
     */
    private $workers = [];

    /**
     * @param WorkerInterface[] $workers
     */
    public function __construct(array $workers = [])
    {
        foreach ($workers as $worker) {
            $this->addWorker($worker);
        }
    }

    /**
     * @param WorkerInterface $worker
     *
     * @return $this
     */
    public function addWorker(WorkerInterface $worker)
    {
        $this->workers[] = $worker;

        return $this;
    }

    /**
     * Determine and return worker.
     *
     * @param TemplateInterface     $template
     * @param ExtendedFileInterface $file
     * @param MediaType             $mediaType
     *
     * @return WorkerInterface
     */
    public function resolve(TemplateInterface $template, ExtendedFileInterface $file, MediaType $mediaType)
    {
        foreach ($this->workers as $worker) {
            if ($worker->accept($template, $file, $mediaType)) {
                return $worker;
            }
        }

        return null;
    }
}
