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
        $language = $this->getUser()->getInterfaceLanguage();

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
        $dsId = $request->get('ds_id');
        $language = $request->get('language');
        $query = $request->get('query', null);
        $valuesQuery = $request->get('valuesqry', '');

        $data = array();

        $dataSourceRepository = $this->get('datasources.repository');
        $dbPool = $container->dbPool;
        $db = $dbPool->read;

        $select = $db->select()
            ->from($db->prefix . 'elementtype_structure', 'options')
            ->where('ds_id = ?', $dsId)
            ->order('version DESC')
            ->limit(1);

        $options = $db->fetchOne($select);
        if ($options) {
            $options = unserialize($options);

            $sourceId = $options['source_source'];
            $item['source_id'] = $sourceId;
            $source = $dataSourceRepository->getDataSourceById($sourceId, $language);

            if ($query && $valuesQuery) {
                $queryArray = explode('|', $query);
            }

            foreach ($source->getKeys() as $key) {
                if (!empty($query)) {
                    if ($valuesQuery && !in_array($key, $queryArray)) {
                        continue;
                    } elseif (!$valuesQuery && mb_stripos($key, $query) === false) {
                        continue;
                    }
                }

                $data[] = array('key' => $key, 'value' => $key);
            }
        }

        return new JsonResponse(array('data' => $data));
    }
}
