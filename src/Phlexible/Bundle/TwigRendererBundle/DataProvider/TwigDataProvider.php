<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TwigRendererBundle\DataProvider;

use Phlexible\Bundle\ElementRendererBundle\Configurator\RenderConfiguration;
use Phlexible\Bundle\ElementRendererBundle\DataProvider\DataProviderInterface;

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

        $data->language  = $request->attributes->get('language');
        $data->isPreview = $request->attributes->get('preview');

        if ($renderConfiguration->hasFeature('treeNode')) {
            $data->treeNode     = $renderConfiguration->get('treeNode');
            $data->treeContext  = $renderConfiguration->get('treeContext');
        }

        if ($renderConfiguration->hasFeature('teaser')) {
            $data->teaser = $renderConfiguration->get('teaser');
        }

        if ($request->attributes->has('siterootUrl')) {
            $data->siteroot  = $request->attributes->get('siterootUrl')->getSiteroot();
            $data->specialTids = $this->createSpecialTids($renderConfiguration, $renderConfiguration->get('language'));
        }

        if ($renderConfiguration->hasFeature('template')) {
            $template = $renderConfiguration->get('template');
            if (substr($template, 0, 1) !== '@' && substr($template, 0, 2) !== '::') {
                $template = '::' . $template;
            }
            if (substr($template, -10) !== '.html.twig') {
                $template = $template . '.html.twig';
            }
            $renderConfiguration->set('template', $template);
            $data->template = $renderConfiguration->get('template');
        }

        if ($renderConfiguration->hasFeature('element')) {
            $data->contentElement      = $renderConfiguration->get('contentElement');
            //$data->eid                 = $data->contentElement->getEid();
            //$data->version             = $data->contentElement->getVersion();
            //$data->elementLanguage     = $data->contentElement->getLanguage();
            //$data->elementUniqueId     = $data->contentElement->getUniqueId();
            //$data->elementTypeUniqueId = $data->contentElement->getElementtypeUniqueId();
            $data->content             = $data->contentElement->getStructure();

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

        if ($renderConfiguration->hasFeature('form')) {
            $data->forms = $renderConfiguration->get('forms');
            $data->formViews = $renderConfiguration->get('formViews');
        }

        if ($renderConfiguration->hasFeature('businesslogic')) {
            $data->businesslogics = $renderConfiguration->get('businesslogics');
        }

        return $data;
    }

    /**
     * @param RenderConfiguration $renderConfiguration
     * @param string              $language
     *
     * @return array
     */
    private function createSpecialTids(RenderConfiguration $renderConfiguration, $language)
    {
        // TODO: mit antonia klären
        $specialTids = array(
            'default_start_eid'       => -1,
            'default_pw_aendern'      => -1,
            'glossar_eid'             => -1,
            'default_copyright_eid'   => -1,
            'schnellsuche_eid'        => -1,
            'default_quicksearch_eid' => -1,
            'sitemap_eid'             => -1,
        );

        foreach ($renderConfiguration->get('request')->attributes->get('siterootUrl')->getSiteroot()->getSpecialTids(null) as $specialTid) {
            $specialTids[$specialTid['name']] = $specialTid['treeId'];
        }

        foreach ($renderConfiguration->get('request')->attributes->get('siterootUrl')->getSiteroot()->getSpecialTids($language) as $specialTid) {
            $specialTids[$specialTid['name']] = $specialTid['treeId'];
        }

        return $specialTids;
    }
}