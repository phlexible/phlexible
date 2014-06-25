<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\CacheBundle\Collector\Formatter;

use Phlexible\ProfilerComponent\Formatter\FormatterInterface;
use Phlexible\ProfilerComponent\Profile\Profile;

/**
 * Cache formatter
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CacheFormatter implements FormatterInterface
{
    /**
     * {@inheritdoc}
     */
    public function getFormatted(Profile $profile)
    {
        $caches = $profile->getRaw('cache');

        return $caches;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'cache';
    }

    /**
     * {@inheritdoc}
     */
    public function getMenuItem(Profile $profile)
    {
        return array('icon' => 'download-alt');
    }
}
