<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Field Controller
 *
 * @author Matthias Harmuth <mharmuth@brainbits.net>
 * @Route("/datasources/field")
 */
class FieldController extends Controller
{
    /**
     * Return selectfield data for lists
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/suggest", name="datasources_field_suggest")
     * @Security("is_granted('ROLE_ELEMENTS')")
     */
    public function suggestAction(Request $request)
    {
        $id = $request->get('id');
        $dsId = $request->get('ds_id');
        $language = $request->get('language');
        $query = $request->get('query', null);
        $valuesQuery = $request->get('valuesqry', '');

        $data = [];

        $datasourceManager = $this->get('phlexible_data_source.data_source_manager');

        $source = $datasourceManager->find($id);

        $filter = null;
        if ($query && $valuesQuery) {
            $filter = explode('|', $query);
        }

        foreach ($source->getActiveValuesForLanguage($language) as $key => $value) {
            if (!empty($query)) {
                if ($filter && !in_array($value, $filter)) {
                    continue;
                } elseif (!$filter && mb_stripos($value, $query) === false) {
                    continue;
                }
            }

            $data[] = ['key' => $value, 'value' => $value];
        }

        return new JsonResponse(['data' => $data]);
    }
}
