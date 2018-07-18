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

use Phlexible\Component\MediaCache\Domain\CacheItem;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Phlexible\Component\MediaType\Model\MediaType;

/**
 * Delegating cache worker.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DelegatingWorker implements WorkerInterface
{
    /**
     * @var WorkerResolver
     */
    private $workerResolver;

    /**
     * @param WorkerResolver $workerResolver
     */
    public function __construct(WorkerResolver $workerResolver)
    {
        $this->workerResolver = $workerResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(TemplateInterface $template, InputDescriptor $input, MediaType $mediaType)
    {
        $worker = $this->workerResolver->resolve($template, $input, $mediaType);

        if (!$worker) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function process(CacheItem $cacheItem, TemplateInterface $template, InputDescriptor $input, MediaType $mediaType)
    {
        $worker = $this->workerResolver->resolve($template, $input, $mediaType);

        if (!$worker) {
            return;
        }

        $worker->process($cacheItem, $template, $input, $mediaType);
    }
}
