<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaCacheBundle\Change;

use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;

/**
 * Template change.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Change
{
    /**
     * @var ExtendedFileInterface
     */
    private $file;

    /**
     * @var TemplateInterface
     */
    private $template;

    /**
     * @var CacheItem
     */
    private $cacheItem;

    /**
     * @param ExtendedFileInterface $file
     * @param TemplateInterface     $template
     * @param CacheItem             $cacheItem
     */
    public function __construct(ExtendedFileInterface $file, TemplateInterface $template, CacheItem $cacheItem)
    {
        $this->file = $file;
        $this->template = $template;
        $this->cacheItem = $cacheItem;
    }

    /**
     * @return ExtendedFileInterface
     */
    public function getFile()
    {
        return $this->file;
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
