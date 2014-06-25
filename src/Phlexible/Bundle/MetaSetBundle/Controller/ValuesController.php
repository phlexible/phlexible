<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Values controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/metasets/values")
 * @Security("is_granted('metasets')")
 */
class ValuesController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("", name="metasets_values")
     */
    public function valuesAction(Request $request)
    {
        $sourceId = $request->get('source_id');
        $language = $request->get('language', 'en');

        $repository = $this->get('datasources.repository');
        $datasource = $repository->getDataSourceById($sourceId, $language);
        $keys = $datasource->getKeys();

        $data = array();
        foreach ($keys as $key) {
            if (!$key) {
                continue;
            }

            $data[] = array('key' => $key, 'value' => $key);
        }

        return new JsonResponse(array('values' => $data));
    }
}
