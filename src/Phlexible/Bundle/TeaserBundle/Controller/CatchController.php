<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Controller;

use Phlexible\Bundle\TeaserBundle\Entity\ElementCatch;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
     * @Route("/sortfields", name="teasers_catch_sortfields")
     */
    public function sortfieldsAction()
    {
        $query = $this->getParam('query');
        $elementtypeIds = explode(',', $query);

        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');

        $dsIds = null;
        foreach ($elementtypeIds as $elementtypeId) {
            $elementtype = $elementtypeService->find($elementtypeId);
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
                'title' => $node->getName() . ' (' . $node->getLabels(
                        'fieldlabel',
                        $this->getUser()->getInterfaceLanguage()
                    ) . ')',
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
                'ds_id' => ElementCatch::SORT_TITLE_BACKEND,
                'title' => $translator->trans('elements.backendTitle', array(), 'gui'),
                'icon'  => '',
            ),
            array(
                'ds_id' => ElementCatch::SORT_TITLE_PAGE,
                'title' => $translator->trans('elements.pageTitle', array(), 'gui'),
                'icon'  => '',
            ),
            array(
                'ds_id' => ElementCatch::SORT_TITLE_NAVIGATION,
                'title' => $translator->trans('elements.navigationTitle', array(), 'gui'),
                'icon'  => '',
            ),
            array(
                'ds_id' => ElementCatch::SORT_PUBLISH_DATE,
                'title' => $translator->trans('elements.publishDate', array(), 'gui'),
                'icon'  => '',
            ),
            array(
                'ds_id' => ElementCatch::SORT_CUSTOM_DATE,
                'title' => $translator->trans('elements.customDate', array(), 'gui'),
                'icon'  => '',
            )
        );

        $this->_response->setResult(true, $query, 'Matching fields.', $result);
    }

    /**
     * List all element types
     *
     * @Route("/elementtypes", name="teasers_catch_elementtypes")
     */
    public function elementtypesAction()
    {
        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');
        $elementtypes = $elementtypeService->findElementtypeByType('full');

        $data = array();
        foreach ($elementtypes as $elementtype) {
            $data[$elementtype->getTitle() . $elementtype->getId()] = array(
                'id'    => $elementtype->getId(),
                'title' => $elementtype->getTitle(),
                'icon'  => '/bundles/elementtypes/elementtypes/' . $elementtype->getIcon(),
            );
        }
        ksort($data);
        $data = array_values($data);

        $this->getResponse()->setAjaxPayload(array('elementtypes' => $data));
    }

    /**
     * @todo
     *
     * @Route("/metakey", name="teasers_catch_metakey")
     */
    public function metakeyAction()
    {
        $metaSetManager = Makeweb_Elements_Element_Version_MetaSet_Manager::getInstance();
        $keys = $metaSetManager->getKeys();

        $translator = $this->get('translator');

        $result = array(
            array('key' => '', 'value' => $translator->get('teasers.no_filter', array(), 'gui')),
        );

        foreach ($keys as $key) {
            $result[] = array(
                'key'   => $key,
                'value' => $key,
            );
        }

        $this->getResponse()->setAjaxPayload(array('metakeys' => $result));
    }

    /**
     * List all element types
     *
     * @todo
     *
     * @Route("/metakeywords", name="teasers_catch_metakeywords")
     */
    public function metakeywordsAction()
    {
        $key = $this->getParam('key');
        $language = $this->getParam('language');

        $metaSetManager = Makeweb_Elements_Element_Version_MetaSet_Manager::getInstance();

        $data = array();
        $values = $metaSetManager->getMetaKeyValues($key, $language);
        foreach ($values as $value) {
            $data[]['keyword'] = $value;
        }

        $this->getResponse()->setAjaxPayload(array('meta_keywords' => $data));
    }

    /**
     * List all available filters
     *
     * @Route("/filters", name="teasers_catch_filters")
     */
    public function filtersAction()
    {
        $data = array();

        $filters = $this->componentCallback->getCatchFilter();

        foreach ($filters as $name => $class) {
            $data[] = array(
                'name'  => $name,
                'class' => $class,
            );
        }

        $this->getResponse()->setAjaxPayload(array('filters' => $data));
    }

    /**
     * @Route("/create", name="teasers_catch_create")
     */
    public function createAction()
    {
        $teaserService = $this->get('phlexible_teaser.teaser_service');

        $teaserService->createCatch(
            $this->getParam('tree_id'),
            $this->getParam('eid'),
            $this->getParam('layoutarea_id')
        );

        $this->_response->setResult(true, 0, 'Catch created.');
    }

    /**
     * @Route("/save", name="teasers_catch_save")
     */
    public function saveAction()
    {
        $catchRepository = $this->get('phlexible_teaser.teaser_service');

        $notEmpty = true;
        $i = 1;

        $metaFilter = new \Zend_Filter();
        $metaFilter->addFilter(new \Zend_Filter_StringTrim());
        $metaFilter->addFilter(new \Zend_Filter_StripTags());

        $catchMetaSearch = array();
        do {
            if ($this->hasParam('catch_meta_key_' . $i) &&
                $this->hasParam('catch_meta_keywords_' . $i)
            ) {
                $catchMetaSearchKey = $metaFilter->filter($this->getParam('catch_meta_key_' . $i));
                $catchMetaSearchKeyword = $metaFilter->filter($this->getParam('catch_meta_keywords_' . $i));
                $i++;
                if (strlen($catchMetaSearchKey) && strlen($catchMetaSearchKeyword)) {
                    $catchMetaSearch[$catchMetaSearchKey] = $catchMetaSearchKeyword;
                }
            } else {
                $notEmpty = false;
            }
        } while ($notEmpty);

        $catch = $catchRepository->find($this->getParam('id'));
        $catch
            ->setTreeId($this->getParam('for_tree_id_hidden'))
            ->setElementtypeIds($this->getParam('catch_element_type_id'))
            ->setNavigation($this->getParam('catch_in_navigation') === 'on')
            ->setMaxDepth($this->getParam('catch_max_depth'))
            ->setSortField($this->getParam('catch_sort_field'))
            ->setSortOrder($this->getParam('catch_sort_order'))
            ->setFilter($this->getParam('catch_filter'))
            ->setMaxResults($this->getParam('catch_max_elements'))
            ->setRotation($this->getParam('catch_rotation') === 'on')
            ->setPoolSize($this->getParam('catch_pool_size'))
            ->setResultsPerPage($this->getParam('catch_elements_per_page'))
            ->setMetaSearch($catchMetaSearch);
        $catchRepository->save($catch);

        $this->_response->setResult(true, 0, 'Catch created.');
    }
}
