<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaCache\CacheIdStrategy;

use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;

/**
 * Cache id strategy interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface CacheIdStrategyInterface
{
    /**
     * @param TemplateInterface     $template
     * @param ExtendedFileInterface $file
     *
     * @return string
     */
    public function createCacheId(TemplateInterface $template, ExtendedFileInterface $file);
}
