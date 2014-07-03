<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\CacheBundle\Controller;

use Phlexible\Component\Formatter\AgeFormatter;
use Phlexible\Component\Formatter\FilesizeFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Data controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/cache")
 * @Security("is_granted('cache')")
 */
class DataController extends Controller
{
    /**
     * @return JsonResponse
     * @Route("/data", name="cache_data")
     */
    public function dataAction()
    {
        $data = array();

        foreach ($this->get('caches') as $name => $cache) {
            $stats = $cache->getStats();

            $time      = time();
            $upTime    = $stats['uptime'];
            $startTime = $time - $upTime;

            $server = array(
                '01 Name'                 => $name,
                '02 Type'                 => get_class($cache),
                '03 Namespace'            => $cache->getNamespace(),
                '04 Starttime'            => 0,
                '05 Uptime'               => 0,
                '06 Used cache size'      => 0,
                '07 Available cache size' => 0,
                '08 Total cache size'     => 0,
                '09 requests'             => 0,
                '10 Hits'                 => 0,
                '11 Misses'               => 0,
                'charts'                  => null,
            );

            if (null !== $stats) {
                $hits   = (int) $stats['hits'];
                $misses = (int) $stats['misses'];
                $reqs   = $hits + $misses;

                $ageFormatter = new AgeFormatter();
                $filesizeFormatter = new FilesizeFormatter();

                $server['04 Starttime']            = date('Y-m-d H:i:s', $startTime);
                $server['05 Uptime']               = $ageFormatter->formatTimestamp($startTime, $time);
                $server['06 Used cache size']      = $filesizeFormatter->formatFilesize($stats['memory_usage']);
                $server['07 Available cache size'] = $filesizeFormatter->formatFilesize($stats['memory_available']);
                $server['08 Total cache size']     = $filesizeFormatter->formatFilesize($stats['memory_usage'] + $stats['memory_available']);
                $server['09 Requests']             = $reqs;
                $server['10 Hits']                 = (int) $stats['hits'];
                $server['11 Misses']               = (int) $stats['misses'];

                $limit = $stats['memory_available'];
                $used  = $stats['memory_usage'];
                $free  = $limit - $used;

                $server['charts']['hits_misses'] = $reqs ? array(round(100 / $reqs * $hits), round(100 / $reqs * $misses)) : null;
                $server['charts']['usage'] = array(
                    (float) number_format(100 / $limit * $free, 2),
                    (float) number_format(100 / $limit * $used, 2)
                );
            }

            $data[$name] = $server;
        }

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/flush", name="cache_flush")
     */
    public function flushAction(Request $request)
    {
        $name = $request->get('name');
        $cache = $this->get($name);
        $cache->flushAll();

        return new ResultResponse(true);
    }
}
