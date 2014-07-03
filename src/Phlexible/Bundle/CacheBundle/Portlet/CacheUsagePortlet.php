<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\CacheBundle\Portlet;

use Phlexible\Bundle\CacheBundle\Cache\CacheCollection;
use Phlexible\Bundle\DashboardBundle\Portlet\Portlet;
use Phlexible\Component\Formatter\FilesizeFormatter;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Cache usage portlet
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CacheUsagePortlet extends Portlet
{
    /**
     * @var CacheCollection
     */
    private $caches;

    /**
     * @param TranslatorInterface $translator
     * @param CacheCollection     $caches
     */
    public function __construct(TranslatorInterface $translator, CacheCollection $caches)
    {
        $this
            ->setId('cache-usage-portlet')
            ->setTitle($translator->trans('cache.cache_usage', array(), 'gui'))
            ->setClass('Phlexible.cache.portlet.CacheUsage')
            ->setIconClass('p-cache-stats-icon')
            ->setResource('debug');

        $this->caches = $caches;
    }

    /**
     * Return Portlet data
     *
     * @return array
     */
    public function getData()
    {
        $data = array();

        foreach ($this->caches as $name => $cache) {
            $stats = $cache->getStats();
            //$hits = $stats['hits'];
            //$misses = $stats['misses'];
            //$uptime = $stats['uptime'];
            $memoryUsage = $stats['memory_usage'];
            $memoryAvailable = $stats['memory_available'];
            $memoryTotal = $memoryUsage + $memoryAvailable;

            $percent = $memoryTotal ? $memoryUsage / $memoryTotal : 0;

            $formatter = new FilesizeFormatter();
            $data[] = array(
                'title'   => $name,
                'used'    => $formatter->formatFilesize($memoryUsage),
                'free'    => $formatter->formatFilesize($memoryAvailable),
                'total'   => $formatter->formatFilesize($memoryTotal),
                'percent' => $percent,
            );

        }

        return $data;
    }
}
