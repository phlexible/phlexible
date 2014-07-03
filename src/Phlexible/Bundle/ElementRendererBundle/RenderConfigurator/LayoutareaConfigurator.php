<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementRendererBundle\RenderConfigurator;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementRendererBundle\RenderConfiguration;
use Phlexible\Bundle\TeaserBundle\Teaser\TeaserService;
use Phlexible\Bundle\TreeBundle\Tree\Node\TreeNodeInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Layout area configurator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LayoutareaConfigurator implements ConfiguratorInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var TeaserService
     */
    private $teaserService;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param LoggerInterface          $logger
     * @param ElementService           $elementService
     * @param TeaserService            $teaserService
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger,
        ElementService $elementService,
        TeaserService $teaserService)
    {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
        $this->elementService = $elementService;
        $this->teaserService = $teaserService;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(Request $request, RenderConfiguration $renderConfiguration)
    {
        if (!$renderConfiguration->hasFeature('treeNode')) {
            return;
        }

        // Before Init Teasers Event
        /*
        $beforeEvent = new \Makeweb_Renderers_Event_BeforeInitTeasers($this);
        if (!$this->dispatcher->dispatch($beforeEvent))
        {
            return false;
        }
        */

        $elementtypeId = $renderConfiguration->get('contentElement')->getElementtypeId();
        $elementtypeService = $this->elementService->getElementtypeService();
        $elementtype = $elementtypeService->findElementtype($elementtypeId);

        $layouts = array();
        $layoutareas = array();
        foreach ($elementtypeService->findElementtypeByType('layout') as $layoutarea) {
            if (in_array($elementtype->getId(), $elementtypeService->findAllowedParentIds($layoutarea))) {
                $layoutareas[] = $layoutarea;
            }
        }

        /* @var $treeNode TreeNodeInterface */
        $treeNode = $renderConfiguration->get('treeNode');
        $tree = $treeNode->getTree();
        $treeNodePath = $tree->getPath($treeNode);

        $language = $request->attributes->get('language');
        $availableLanguages = $request->attributes->get('availableLanguages');
        $isPreview = true;

        $areas = array();

        foreach ($layoutareas as $layoutarea) {
            //$beforeAreaEvent = new Brainbits_Event_Notification(new stdClass(), 'before_area');
            //$this->_dispatcher->dispatch($beforeAreaEvent);

            //$templateFilename = '';
            //$templates = $layoutElementTypeVersion->getTemplates();

            //if (count($templates))
            //{
            //    $template = current($templates);
            //    $templateFilename = $template->getFilename();
            //}

            //$this->_debugTime('initTeasers - Layoutarea');
            //$this->_debugLine('Layoutarea: ' . $layoutElementTypeVersion->getTitle(), 'notice');

            $teasers = $this->teaserService->findForLayoutAreaAndTreeNodePath($layoutarea, $treeNodePath);

            $areas[$layoutarea->getUniqueId()] = array(
                'title'     => $layoutarea->getTitle(),
                'template'  => $layoutarea->getUniqueId(),
                'unique_id' => $layoutarea->getUniqueId(),
                'children'  => $teasers
            );

            //$areaEvent = new Brainbits_Event_Notification(new stdClass(), 'area');
            //$this->_dispatcher->dispatch($areaEvent);
        }

        $renderConfiguration
            ->addFeature('layoutarea')
            ->set('layoutareas', $areas);

        // Before Init Teasers Event
        /*
        $event = new Makeweb_Renderers_Event_InitTeasers($this);
        $this->_dispatcher->dispatch($event);
        */
    }

}
