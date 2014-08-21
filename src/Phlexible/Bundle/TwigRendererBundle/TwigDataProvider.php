<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TwigRendererBundle;

use Phlexible\Bundle\ElementRendererBundle\DataProviderInterface;
use Phlexible\Bundle\ElementRendererBundle\RenderConfiguration;
use Phlexible\Bundle\ElementBundle\ElementStructure\ElementStructureWrap;

/**
 * Twig data provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TwigDataProvider implements DataProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function provide(RenderConfiguration $renderConfiguration)
    {
        $data = new \ArrayObject(array(), \ArrayObject::ARRAY_AS_PROPS);

        $request = $renderConfiguration->get('request');

        $data->request   = $request;
        $data->language  = $request->attributes->get('language');
        $data->isPreview = true;//(bool) $renderRequest->getVersionStrategy() === 'latest';

        if ($renderConfiguration->hasFeature('treeNode')) {
            $treeNode = $renderConfiguration->get('treeNode');
            $data->treeNode     = $treeNode;
            $data->treeContext  = $renderConfiguration->get('treeContext');
            $data->tid          = $treeNode->getId();
            $data->parentTid    = $treeNode->getParentId();
            $data->isPublished  = false;//(bool) $treeNode->isPublished($renderConfiguration->get('elementLanguage'));
            $data->isRestricted = false;//(bool) $treeNode->isRestricted($version);
            $data->inNavigation = false;//(bool) $treeNode->inNavigation($version);
        }

        if ($request->attributes->has('siterootUrl')) {
            $data->siterootId  = $request->attributes->get('siterootUrl')->getSiteroot()->getId();
            $data->specialTids = $this->createSpecialTids($renderConfiguration, $data);
        }

        if ($renderConfiguration->hasFeature('template')) {
            $renderConfiguration->set('template', '::' . $renderConfiguration->get('template') . '.html.twig');
            $data->template = $renderConfiguration->get('template');
        }

        if ($renderConfiguration->hasFeature('element')) {
            $data->contentElement      = $renderConfiguration->get('contentElement');
            $data->eid                 = $data->contentElement->getEid();
            $data->version             = $data->contentElement->getVersion();
            $data->elementLanguage     = $data->contentElement->getLanguage();
            $data->elementUniqueId     = $data->contentElement->getUniqueId();
            $data->elementTypeUniqueId = $data->contentElement->getElementtypeUniqueId();
            $data->content             = new ElementStructureWrap($data->contentElement->getStructure());//new ElementVersionDataWrap($renderConfiguration->get('elementData')->getTree());
            $data->publishDate         = new \DateTime();//$renderConfiguration->get('treeNode')->getPublishDate($renderConfiguration->get('elementLanguage'));

            /* @deprecated */ $data->createDate       = $data->publishDate;
            $data->contentLanguages = array('de');
        }

        if ($renderConfiguration->hasFeature('navigation')) {
            $data->navigation = $renderConfiguration->get('navigations');
        }

        if ($renderConfiguration->hasFeature('layoutarea')) {
            $data->teasers = $renderConfiguration->get('layoutareas');
        }

        if ($renderConfiguration->hasFeature('context')) {
            $data->country   = $renderConfiguration->get('context')->getCountry();
            $data->languages = $renderConfiguration->get('context')->getLanguagesForTid($renderConfiguration->get('treeNode')->getTid());
        }

        if ($renderConfiguration->hasFeature('catch')) {
            $data->catched = $renderConfiguration->get('catchResults');

        }
        if ($renderConfiguration->hasFeature('locale')) {
            $this->_assignLocaleDate($renderConfiguration, $data);
        }

        return $data;
    }

    /**
     * @param RenderConfiguration $renderConfiguration
     *
     * @return array
     */
    protected function createSpecialTids(RenderConfiguration $renderConfiguration)
    {
        $specialTids = $renderConfiguration->get('request')->attributes->get('siterootUrl')->getSiteroot()->getSpecialTids('de');
        $specialTids += array(
            'default_start_eid'       => -1,
            'default_pw_aendern'      => -1,
            'glossar_eid'             => -1,
            'default_copyright_eid'   => -1,
            'schnellsuche_eid'        => -1,
            'default_quicksearch_eid' => -1,
            'sitemap_eid'             => -1,
        );

        return $specialTids;
    }

    protected function _assignLocaleDate(RenderConfiguration $renderConfiguration, $data)
    {
        switch ($renderConfiguration->get('request')->attributes->get('language'))
        {
            case 'de':
                $localeDate = '%d.%m.%Y';
                $localeTime = '%H:%M:%S';
                $localeTimeShort = '%H:%M';
                break;

            case 'gb':
                $localeDate = '%d/%m/%Y';
                $localeTime = '%I:%M:%S %p';
                $localeTimeShort = '%I:%M %p';
                break;

            case 'us':
                $localeDate = '%Y-%m-%d';
                $localeTime = '%I:%M:%S %p';
                $localeTimeShort = '%I:%M %p';
                break;

            default:
                $localeDate = '%Y-%m-%d';
                $localeTime = '%I:%M:%S %p';
                $localeTimeShort = '%I:%M %p';

                break;
        }

        $data->localeDate          = $localeDate;
        $data->localeTime          = $localeTime;
        $data->localeTimeShort     = $localeTimeShort;
        $data->localeDateTime      = $localeDate . ' ' . $localeTime;
        $data->localeDateTimeShort = $localeDate . ' ' . $localeTimeShort;
    }

}