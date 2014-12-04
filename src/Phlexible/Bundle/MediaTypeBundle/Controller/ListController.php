<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTypeBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * List controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/mediatypes")
 * @Security("is_granted('ROLE_MEDIA_TYPES')")
 */
class ListController extends Controller
{
    /**
     * List media types
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/list", name="mediatypes_list")
     */
    public function listAction(Request $request)
    {
        $mediaTypeManager = $this->get('phlexible_media_type.media_type_manager');
        $iconResolver = $this->get('phlexible_media_type.icon_resolver');

        $mediaTypes = [];
        foreach ($mediaTypeManager->findAll() as $mediaType) {
            $mediaTypes[] = [
                'id'        => $mediaType->getName(),
                'key'       => $mediaType->getName(),
                'upperkey'  => strtoupper($mediaType->getName()),
                'type'      => $mediaType->getCategory(),
                'de'        => $mediaType->getTitle('de'),
                'en'        => $mediaType->getTitle('en'),
                'mimetypes' => $mediaType->getMimetypes(),
                'icon16'    => (bool) $iconResolver->resolve($mediaType, 16),
                'icon32'    => (bool) $iconResolver->resolve($mediaType, 32),
                'icon48'    => (bool) $iconResolver->resolve($mediaType, 48),
                'icon256'   => (bool) $iconResolver->resolve($mediaType, 256),
            ];
        }

        return new JsonResponse([
            'totalCount'    => count($mediaTypes),
            'documenttypes' => $mediaTypes
        ]);
    }
}
