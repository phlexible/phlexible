<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaCache;

/**
 * Media cache events.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaCacheEvents
{
    /**
     * Fired before a media cache item is saved.
     */
    const BEFORE_SAVE_CACHE_ITEM = 'phlexible_media_cache.before_save_cache_item';

    /**
     * Fired after a media cache item is saved.
     */
    const SAVE_CACHE_ITEM = 'phlexible_media_cache.save_cache_item';
}
