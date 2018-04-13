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
 * Cache worker interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface WorkerInterface
{
    /**
     * Are the given template and asset supported?
     *
     * @param TemplateInterface $template
     * @param InputDescriptor   $input
     * @param MediaType         $mediaType
     *
     * @return bool
     */
    public function accept(TemplateInterface $template, InputDescriptor $input, MediaType $mediaType);

    /**
     * Process template and file.
     *
     * @param CacheItem         $cacheItem
     * @param TemplateInterface $template
     * @param InputDescriptor   $input
     * @param MediaType         $mediaType
     */
    public function process(CacheItem $cacheItem, TemplateInterface $template, InputDescriptor $input, MediaType $mediaType);
}
