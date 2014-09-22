<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Controller;

use Phlexible\Bundle\ElementtypeBundle\SelectFieldProvider\SelectFieldProviderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Selectfield Controller
 *
 * @author Matthias Harmuth <mharmuth@brainbits.net>
 * @Route("/elementtypes/selectfield")
 * @Security("is_granted('elementtypes')")
 */
class SelectfieldController extends Controller
{
    /**
     * Return selectfield data for lists
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/list", name="elementtypes_selectfield_list")
     */
    public function listAction(Request $request)
    {
        $providerClassname = $request->get('provider');
        $language = $this->getUser()->getInterfaceLanguage('en');

        $data = array();

        if (class_exists($providerClassname)) {
            $provider = new $providerClassname();

            if ($provider instanceof SelectFieldProviderInterface) {
                $data = $provider->get($language);
            }
        }

        return new JsonResponse(array('data' => $data));
    }

    /**
     * Return selectfield data for lists
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/suggest", name="elementtypes_selectfield_suggest")
     */
    public function suggestAction(Request $request)
    {
        $id = $request->get('id');
        $dsId = $request->get('ds_id');
        $language = $request->get('language');
        $query = $request->get('query', null);
        $valuesQuery = $request->get('valuesqry', '');

        $data = array();

        $datasourceManager = $this->get('phlexible_data_source.data_source_manager');

        $source = $datasourceManager->find($id);

        $filter = null;
        if ($query && $valuesQuery) {
            $filter = explode('|', $query);
        }

        foreach ($source->getActiveValuesForLanguage($language) as $key => $value) {
            if (!empty($query)) {
                if ($filter && !in_array($key, $filter)) {
                    continue;
                } elseif (!$filter && mb_stripos($key, $query) === false) {
                    continue;
                }
            }

            $data[] = array('key' => $key, 'value' => $key);
        }

        return new JsonResponse(array('data' => $data));
    }
}
