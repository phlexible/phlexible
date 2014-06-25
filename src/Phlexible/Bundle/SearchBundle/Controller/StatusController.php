<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Status controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/status/search")
 * @Security("is_granted('debug')")
 */
class StatusController extends Controller
{
    /**
     * Return Search results
     *
     * @param Request $request
     *
     * @return Response
     * @Route("", name="search_status")
     */
    public function indexAction(Request $request)
    {
        $query = $request->query->get('query');
        $limit = $request->query->get('limit');
        $start = $request->query->get('start');

        $searchProviders = $this->get('search.providers');

        $content = '';
        $content .= '<h3>Registered Search Providers:</h3><table><tr><th>Class</th><th>Resource</th><th>Search key</th></tr>';
        foreach ($searchProviders as $searchProvider) {
            $content .= '<tr>';
            $content .= '<td>'.get_class($searchProvider).'</td>';
            $content .= '<td>'.$searchProvider->getResource().'</td>';
            $content .= '<td>'.$searchProvider->getSearchKey().'</td>';
            $content .= '</tr>';
        }
        $content .= '</table>';

        $content .= '<h3>Search:</h3>';
        $content .= '<form><input name="query" value="' . $query . '"/><input type="submit" value="send" /></form>';

        if ($query) {
            $content .= '<h3>Results:</h3>';
            $search = $this->getContainer()->get('search.search');
            $results = $search->search($query);
            $content .= '<pre>';
            $content .= print_r($results, true);
            $content .= '</pre>';
        }

        return new Response($content);
    }
}