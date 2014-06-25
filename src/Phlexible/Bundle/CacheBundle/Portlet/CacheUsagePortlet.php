<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\CacheBundle\Portlet;

use Brainbits_Format_Filesize as FilesizeFormatter;
use Phlexible\Bundle\CacheBundle\Cache\CacheCollection;
use Phlexible\Bundle\DashboardBundle\Portlet\AbstractPortlet;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Cache usage portlet
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CacheUsagePortlet extends AbstractPortlet
{
    /**
     * @var array
     */
    protected $cache;

    /**
     * @param TranslatorInterface $translator
     * @param CacheCollection     $caches
     */
    public function __construct(TranslatorInterface $translator, CacheCollection $caches)
    {
        $this->id        = 'cache-usage-portlet';
        $this->title     = $translator->trans('cache.cache_usage', array(), 'gui');
        $this->class     = 'Phlexible.cache.portlet.CacheUsage';
        $this->iconClass = 'p-cache-stats-icon';
        $this->resource  = 'debug';

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

            $data[] = array(
                'title'   => $name,
                'used'    => FilesizeFormatter::format($memoryUsage),
                'free'    => FilesizeFormatter::format($memoryAvailable),
                'total'   => FilesizeFormatter::format($memoryTotal),
                'percent' => $percent,
            );

        }

        return $data;
    }
}
