<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DocumenttypeBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * List controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/documenttypes")
 * @Security("is_granted('ROLE_DOCUMENTTYPES')")
 */
class ListController extends Controller
{
    /**
     * List documenttypes
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/list", name="documenttypes_list")
     */
    public function listAction(Request $request)
    {
        $documenttypeManager = $this->get('phlexible_documenttype.documenttype_manager');
        $iconResolver = $this->get('phlexible_documenttype.icon_resolver');

        $allDocumenttypes = $documenttypeManager->findAll();

        $documenttypes = [];
        foreach ($allDocumenttypes as $documenttype) {
            $documenttypes[] = [
                'id'        => $documenttype->getKey(),
                'key'       => $documenttype->getKey(),
                'upperkey'  => strtoupper($documenttype->getKey()),
                'type'      => $documenttype->getType(),
                'de'        => $documenttype->getTitle('de'),
                'en'        => $documenttype->getTitle('en'),
                'mimetypes' => $documenttype->getMimetypes(),
                'icon16'    => (bool) $iconResolver->resolve($documenttype, 16),
                'icon32'    => (bool) $iconResolver->resolve($documenttype, 32),
                'icon48'    => (bool) $iconResolver->resolve($documenttype, 48),
                'icon256'   => (bool) $iconResolver->resolve($documenttype, 256),
            ];
        }

        return new JsonResponse([
            'totalCount'    => count($documenttypes),
            'documenttypes' => $documenttypes
        ]);
    }
}
