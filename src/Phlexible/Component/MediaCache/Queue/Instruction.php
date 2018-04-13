<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaCache\Queue;

use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Component\MediaCache\Worker\InputDescriptor;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;

/**
 * A batch represents a file/template cross combination.
 * Results in a list of queue items.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Instruction
{
    /**
     * @var InputDescriptor
     */
    private $input;

    /**
     * @var TemplateInterface
     */
    private $template;

    /**
     * @var CacheItem
     */
    private $cacheItem;

    /**
     * @param InputDescriptor   $input
     * @param TemplateInterface $template
     * @param CacheItem         $cacheItem
     */
    public function __construct(InputDescriptor $input, TemplateInterface $template, CacheItem $cacheItem)
    {
        $this->input = $input;
        $this->template = $template;
        $this->cacheItem = $cacheItem;
    }

    /**
     * @return InputDescriptor
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @return TemplateInterface
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @return CacheItem
     */
    public function getCacheItem()
    {
        return $this->cacheItem;
    }
}
