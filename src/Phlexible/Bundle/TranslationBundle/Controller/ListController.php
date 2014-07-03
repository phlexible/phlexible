<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TranslationBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * List controller
 *
 * @author Phillip Look <plook@brainbits.net>
 * @Route("/translations")
 * @Security("is_granted('translations')")
 */
class ListController extends Controller
{
    /**
     * List translations
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/list", name="translations_list")
     * @Method({"GET", "POST"})
     * @ApiDoc(
     *   description="List translations"
     * )
     */
    public function listAction(Request $request)
    {
        $keySearch = $request->get('key');
        $deSearch = $request->get('de_value');
        $deEmpty = $request->get('de_empty');
        $enSearch = $request->get('en_value');
        $enEmpty = $request->get('en_empty');

        $accessor = $this->get('translations.catalog_accessor');

        $deData = $accessor->getCatalogues('de')->all();
        $enData = $accessor->getCatalogues('en')->all();

        $items = array();
        foreach ($deData as $domain => $values) {
            foreach ($values as $key => $value) {
                $deValue = $value;
                $enValue = !empty($enData[$domain][$key]) ? $enData[$domain][$key] : '';

                if ($keySearch && strpos($key, $keySearch) === false) {
                    continue;
                }
                if ($deSearch && strpos($deValue, $deSearch) === false) {
                    continue;
                }
                if ($deEmpty && $deValue) {
                    continue;
                }
                if ($enSearch && strpos($enValue, $enSearch) === false) {
                    continue;
                }
                if ($enEmpty && $enValue) {
                    continue;
                }
                $items[] = array(
                    'id'     => $domain . '.' . $key,
                    'domain' => $domain,
                    'key'    => $key,
                    'de'     => $deValue,
                    'en'     => $enValue,
                );
            }
        }

        return new JsonResponse(array(
            'total' => count($items),
            'items' => $items,
        ));
    }
}
