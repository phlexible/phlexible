<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SearchBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Search controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/search/search")
 */
class SearchController extends Controller
{
    /**
     * Return search results
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("", name="search_search")
     * @Method({"GET", "POST"})
     * @ApiDoc(
     *   description="Search",
     *   requirements={
     *     {"name"="query", "dataType"="string", "required"=true, "description"="Search query"}
     *   },
     *   filters={
     *     {"name"="limit", "dataType"="integer", "default"=8, "description"="Limit results"},
     *     {"name"="start", "dataType"="integer", "default"=0, "description"="Result offset"}
     *   }
     * )
     */
    public function indexAction(Request $request)
    {
        $query = $request->get('query');
        $limit = $request->get('limit', 8);
        $start = $request->get('start', 0);

        $search = $this->get('phlexible_search.search');
        $results = $search->search($query);

        return new JsonResponse(array(
            'totalCount' => count($results),
            'results'    => array_slice($results, $start, $limit)
        ));
    }
}
