<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Data controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/datasources")
 * @Security("is_granted('datasources')")
 */
class DataController extends Controller
{
    /**
     * Return something
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/add", name="datasources_add")
     */
    public function addAction(Request $request)
    {
        $sourceId = $request->get('source_id');
        $key      = $request->get('key');
        $language = $request->get('language', 'de');

        $dataSourcesRepository = $this->get('datasources.repository');

        // load
        $source = $dataSourcesRepository->getDataSourceById($sourceId, $language);

        // add new key
        $source->addKey($key, false);

        // save
        $dataSourcesRepository->save($source, $this->getUser()->getId());

        return new ResultResponse(true);
    }

    /**
     * Return something
     *
     * @return JsonResponse
     * @Route("/list", name="datasources_list")
     */
    public function listAction()
    {
        $dataSourcesRepository = $this->get('datasources.repository');

        $titles = $dataSourcesRepository->getAllDataSourceTitles();
        asort($titles);

        $sources = array();
        foreach ($titles as $id => $title) {
            $sources[] = array('id' => $id, 'title' => $title);
        }

        return new JsonResponse(array('sources' => $sources));
    }
}