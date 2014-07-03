<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\CacheBundle\Collector;

use Phlexible\Bundle\CacheBundle\Cache\CacheCollection;
use Phlexible\Component\Formatter\FilesizeFormatter;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;

/**
 * Cache collector
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CacheCollector implements DataCollectorInterface
{
    /**
     * @var CacheCollection
     */
    private $caches;

    /**
     * @param CacheCollection $caches
     */
    public function __construct(CacheCollection $caches)
    {
        $this->caches = $caches;
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
    public function getFormatterClassname()
    {
        return 'Phlexible\Bundle\CacheBundle\Collector\Formatter\CacheFormatter';
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = array();

        foreach ($this->caches as $name => $cache) {
            $stats = $cache->getStats();

            $formatter = new FilesizeFormatter();

            $available = $formatter->formatFilesize($stats['memory_available']);
            $usage = $formatter->formatFilesize($stats['memory_usage']);

            $data[] = array(
                'name'           => $name,
                'class'          => get_class($cache),
                'available'      => $stats['memory_available'],
                'availableHuman' => $available,
                'usage'          => $stats['memory_usage'],
                'usageHuman'     => $usage,
                'percent'        => $stats['memory_usage'] / $stats['memory_available'],
            );
        }

        return $data;
    }
}
