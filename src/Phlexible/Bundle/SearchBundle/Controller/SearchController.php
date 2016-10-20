<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\SearchBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

        return new JsonResponse([
            'totalCount' => count($results),
            'results'    => array_slice($results, $start, $limit)
        ]);
    }
}
