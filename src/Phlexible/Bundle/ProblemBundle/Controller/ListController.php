<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ProblemBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * List controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/problems")
 * @Security("is_granted('ROLE_PROBLEMS')")
 */
class ListController extends Controller
{
    /**
     * List problems
     *
     * @return JsonResponse
     * @Route("/list", name="problems_list")
     * @Method({"GET", "POST"})
     * @ApiDoc(
     *   description="Return problems"
     * )
     */
    public function listAction()
    {
        $problemFetcher = $this->get('phlexible_problem.problem_fetcher');

        $data = [];
        foreach ($problemFetcher->fetch() as $problem) {
            $data[] = [
                'id'            => strlen($problem->getId()) ? $problem->getId() : md5(serialize($problem)),
                'iconCls'       => $problem->getIconClass(),
                'severity'      => $problem->getSeverity(),
                'msg'           => $problem->getMessage(),
                'hint'          => $problem->getHint(),
                'link'          => $problem->getLink(),
                'createdAt'     => $problem->getCreatedAt()->format('Y-m-d H:i:s'),
                'lastCheckedAt' => $problem->getLastCheckedAt()->format('Y-m-d H:i:s'),
                'source'        => $problem->isLive() ? 'live' : 'cached',
            ];
        }

        return new JsonResponse($data);
    }
}
