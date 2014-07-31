<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\Controller;

use Phlexible\Bundle\DataSourceBundle\Entity\DataSource;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
     * @return JsonResponse
     * @Route("/list", name="datasources_list")
     */
    public function listAction()
    {
        $dataSourceManager = $this->get('phlexible_data_source.data_source_manager');

        $dataSources = $dataSourceManager->findBy(array());

        $sources = array();
        foreach ($dataSources as $dataSource) {
            $sources[] = array(
                'id' => $dataSource->getId(),
                'title' => $dataSource->getTitle()
            );
        }

        return new JsonResponse(array('datasources' => $sources));
    }

    /**
     * Return something
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/create", name="datasources_create")
     */
    public function createAction(Request $request)
    {
        $title = $request->get('title');

        $dataSourceManager = $this->get('phlexible_data_source.data_source_manager');

        $dataSource = new DataSource();
        $dataSource
            ->setTitle($title)
            ->setCreatedAt(new \DateTime())
            ->setCreateUserId($this->getUser()->getId())
            ->setModifiedAt($dataSource->getCreatedAt())
            ->setModifyUserId($dataSource->getCreateUserId());

        try {
            $dataSourceManager->updateDataSource($dataSource);

            $response = new ResultResponse(true);
        } catch (\Exception $e) {
            $response = new ResultResponse(false, $e->getMessage());
        }

        return $response;
    }

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
        $key = $request->get('key');
        $language = $request->get('language', 'de');

        $dataSourceManager = $this->get('phlexible_data_source.data_source_manager');

        // load
        $source = $dataSourceManager->find($sourceId);

        // add new key
        $source->addValueForLanguage($key, false);

        // save
        $dataSourceManager->save($source, $this->getUser()->getId());

        return new ResultResponse(true);
    }
}