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
 * Default cache id strategy
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DefaultCacheIdStrategy implements CacheIdStrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function createCacheId(TemplateInterface $template, ExtendedFileInterface $file)
    {
        $identifiers = [$template->getKey(), $file->getId(), $file->getVersion(), $file->getHash()];

        return md5(implode('__', $identifiers));
    }
}
