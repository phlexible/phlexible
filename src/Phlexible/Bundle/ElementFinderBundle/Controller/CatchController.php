<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\ElementFinderBundle\Entity\ElementFinderConfig;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Catch controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/teasers/catch")
 * @Security("is_granted('teasers')")
 */
class CatchController extends Controller
{
    /**
     * List all sortable fields.
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/sortfields", name="elementfinder_catch_sortfields")
     */
    public function sortfieldsAction(Request $request)
    {
        $query = $request->get('query');
        $elementtypeIds = explode(',', $query);

        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');

        $dsIds = null;
        foreach ($elementtypeIds as $elementtypeId) {
            $elementtype = $elementtypeService->findElementtype($elementtypeId);
            $elementtypeVersion = $elementtypeService->findLatestElementtypeVersion($elementtype);
            $elementtypeStructure = $elementtypeService->findElementtypeStructure($elementtypeVersion);

            $dsIds = (null === $dsIds)
                ? $elementtypeStructure->getAllDsIds()
                : array_intersect($dsIds, $elementtypeStructure->getAllDsIds());
        }

        $result = array();
        foreach ($dsIds as $dsId) {
            $node = $elementtypeStructure->getNode($dsId);

            // skip fields without working title
            if (!strlen($node->getName())) {
                continue;
            }

            // skip fields of types that cannot be sorted by
            static $skipFieldTypes = array(
                'accordion',
                'businesslogic',
                'download',
                'flash',
                'form',
                'group',
                'image',
                'reference',
                'referenceroot',
                'root',
                'tab',
                'table',
                'video',
            );

            $fieldType = $node->getType();
            if (in_array($fieldType, $skipFieldTypes)) {
                continue;
            }

            $result[] = array(
                'ds_id' => $dsId,
                'title' => $node->getName() . ' (' . $node->getLabel('fieldlabel', $this->getUser()->getInterfaceLanguage()) . ')',
                'icon'  => 'm-fields-field_' . $fieldType . '-icon',
            );
        }

        array_multisort(array_column($result, 'title'), $result);

        $translator = $this->get('translator');
        array_unshift(
            $result,
            array(
                'ds_id' => '',
                'title' => $translator->trans('elements.unsorted', array(), 'gui'),
                'icon'  => '',
            ),
            array(
                'ds_id' => ElementFinderConfig::SORT_TITLE_BACKEND,
                'title' => $translator->trans('elements.backend_title', array(), 'gui'),
                'icon'  => '',
            ),
            array(
                'ds_id' => ElementFinderConfig::SORT_TITLE_PAGE,
                'title' => $translator->trans('elements.page_title', array(), 'gui'),
                'icon'  => '',
            ),
            array(
                'ds_id' => ElementFinderConfig::SORT_TITLE_NAVIGATION,
                'title' => $translator->trans('elements.navigation_title', array(), 'gui'),
                'icon'  => '',
            ),
            array(
                'ds_id' => ElementFinderConfig::SORT_PUBLISH_DATE,
                'title' => $translator->trans('elements.publish_date', array(), 'gui'),
                'icon'  => '',
            ),
            array(
                'ds_id' => ElementFinderConfig::SORT_CUSTOM_DATE,
                'title' => $translator->trans('elements.custom_date', array(), 'gui'),
                'icon'  => '',
            )
        );

        return new ResultResponse(true, 'Matching fields.', $result);
    }

    /**
     * List all element types
     *
     * @return JsonResponse
     * @Route("/elementtypes", name="elementfinder_catch_elementtypes")
     */
    public function elementtypesAction()
    {
        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');
        $iconResolver = $this->get('phlexible_element.icon_resolver');

        $elementtypes = $elementtypeService->findElementtypeByType('full');

        $data = array();
        foreach ($elementtypes as $elementtype) {
            $data[$elementtype->getTitle() . $elementtype->getId()] = array(
                'id'    => $elementtype->getId(),
                'title' => $elementtype->getTitle(),
                'icon'  => $iconResolver->resolveElementtype($elementtype),
            );
        }
        ksort($data);
        $data = array_values($data);

        return new JsonResponse(array('elementtypes' => $data));
    }

    /**
     * @return JsonResponse
     * @Route("/metakey", name="elementfinder_catch_metakey")
     */
    public function metakeyAction()
    {
        $metasetManager = $this->get('phlexible_meta_set.meta_set_manager');

        $metasets = $metasetManager->findAll();

        $keys = array();
        foreach ($metasets as $metaset) {
            $keys[] = $metaset->getName();
        }

        $translator = $this->get('translator');

        $result = array(
            array('key' => '', 'value' => $translator->trans('teasers.no_filter', array(), 'gui')),
        );

        foreach ($keys as $key) {
            $result[] = array(
                'key'   => $key,
                'value' => $key,
            );
        }

        return new JsonResponse(array('metakeys' => $result));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/metakeywords", name="elementfinder_catch_metakeywords")
     */
    public function metakeywordsAction(Request $request)
    {
        $name = $request->get('key');
        $language = $request->get('language');

        // TODO: repair
        $metasetManager = $this->get('phlexible_meta_set.meta_set_manager');
        $metaset = $metasetManager->findOneByName($name);

        $data = array();
        //$values = $metaset->getMetaKeyValues($key, $language);
        foreach ($values as $value) {
            $data[]['keyword'] = $value;
        }

        return new JsonResponse(array('meta_keywords' => $data));
    }

    /**
     * List all available filters
     *
     * @return JsonResponse
     * @Route("/filters", name="elementfinder_catch_filters")
     */
    public function filtersAction()
    {
        $data = array();

        // @TODO: repair
        $filters = array();//$this->componentCallback->getCatchFilter();

        foreach ($filters as $name => $class) {
            $data[] = array(
                'name'  => $name,
                'class' => $class,
            );
        }

        return new JsonResponse(array('filters' => $data));
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/create", name="elementfinder_catch_create")
     */
    public function createAction(Request $request)
    {
        $teaserService = $this->get('phlexible_teaser.teaser_service');

        $teaserService->createCatch(
            $request->get('tree_id'),
            $request->get('eid'),
            $request->get('layoutarea_id')
        );

        return new ResultResponse(true, 'Catch created.');
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/save", name="elementfinder_catch_save")
     */
    public function saveAction(Request $request)
    {
        $catchManager = $this->get('phlexible_element_finder.finder_manager');

        $notEmpty = true;
        $i = 1;

        $metaFilter = new \Zend_Filter();
        $metaFilter->addFilter(new \Zend_Filter_StringTrim());
        $metaFilter->addFilter(new \Zend_Filter_StripTags());

        $catchMetaSearch = array();
        do {
            if ($request->get('catch_meta_key_' . $i) &&
                $request->get('catch_meta_keywords_' . $i)
            ) {
                $catchMetaSearchKey = $metaFilter->filter($request->get('catch_meta_key_' . $i));
                $catchMetaSearchKeyword = $metaFilter->filter($request->get('catch_meta_keywords_' . $i));
                $i++;
                if (strlen($catchMetaSearchKey) && strlen($catchMetaSearchKeyword)) {
                    $catchMetaSearch[$catchMetaSearchKey] = $catchMetaSearchKeyword;
                }
            } else {
                $notEmpty = false;
            }
        } while ($notEmpty);

        $catch = $catchManager->findCatch($request->get('id'));

        $catch
            ->setTitle($request->get('title'))
            ->setTreeId($request->get('for_tree_id_hidden'))
            ->setElementtypeIds($request->get('catch_element_type_id') ? explode(',', $request->get('catch_element_type_id')) : array())
            ->setNavigation($request->get('catch_in_navigation') === 'on')
            ->setMaxDepth($request->get('catch_max_depth'))
            ->setSortField($request->get('catch_sort_field'))
            ->setSortOrder($request->get('catch_sort_order'))
            ->setFilter($request->get('catch_filter'))
            ->setMaxResults($request->get('catch_max_elements'))
            ->setRotation($request->get('catch_rotation') === 'on')
            ->setPoolSize($request->get('catch_pool_size'))
            ->setResultsPerPage($request->get('catch_elements_per_page'))
            ->setMetaSearch($catchMetaSearch);

        $catchManager->updateCatch($catch);

        return new ResultResponse(true, 'Catch created.');
    }
}
