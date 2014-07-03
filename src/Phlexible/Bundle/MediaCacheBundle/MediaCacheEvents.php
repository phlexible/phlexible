<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle;

/**
 * Media cache events
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaCacheEvents
{
    /**
     * Fired before a media cache item is saved
     */
    const BEFORE_SAVE_CACHE_ITEM = 'phlexible_media_cache.before_save_cache_item';

    /**
     * Fired after a media cache item is saved
     */
    const SAVE_CACHE_ITEM = 'phlexible_media_cache.save_cache_item';
}