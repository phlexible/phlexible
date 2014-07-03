<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\CacheBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * Status controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/status/cache")
 * @Security("is_granted('debug')")
 */
class StatusController extends Controller
{
    /**
     * Cache status
     *
     * @return Response
     * @Route("", name="cache_status")
     */
    public function indexAction()
    {
        $output =  '<h3>Cache Status</h3>';

        foreach ($this->get('caches') as $name => $cache) {
            $output .= "<h4>$name</h4>";

            $stats = $cache->getStats();

            if ($stats !== null) {
                $output .= "Hits: " . $stats['hits'] . "<br />";
                $output .= "Misses " . $stats['misses'] . "<br />";
                $output .= "Uptime " . $stats['uptime'] . "<br />";
                $output .= "Memory usage " . $stats['memory_usage'] . "<br />";
                $output .= "Memory available " . $stats['memory_available'] . "<br />";
            } else {
                $output .= "No stats provided by cache type<br />";
            }
        }

        return new Response($output);
    }
}