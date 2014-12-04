<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Configurator;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Model\ElementSourceManagerInterface;
use Phlexible\Bundle\ElementRendererBundle\Configurator\RenderConfiguration;
use Phlexible\Bundle\ElementRendererBundle\ElementRendererEvents;
use Phlexible\Bundle\ElementRendererBundle\Event\ConfigureEvent;
use Phlexible\Bundle\ElementRendererBundle\RenderConfigurator\ConfiguratorInterface;
use Phlexible\Bundle\TeaserBundle\ContentTeaser\DelegatingContentTeaserManager;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
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
     * @var ElementService
     */
    private $elementService;

    /**
     * @var ElementSourceManagerInterface
     */
    private $elementSourceManager;

    /**
     * @var DelegatingContentTeaserManager
     */
    private $teaserManager;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ElementService                 $elementService
     * @param ElementSourceManagerInterface  $elementSourceManager
     * @param EventDispatcherInterface       $dispatcher
     * @param LoggerInterface                $logger
     * @param DelegatingContentTeaserManager $teaserManager
     */
    public function __construct(
        ElementService $elementService,
        ElementSourceManagerInterface $elementSourceManager,
        DelegatingContentTeaserManager $teaserManager,
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger)
    {
        $this->elementService = $elementService;
        $this->elementSourceManager = $elementSourceManager;
        $this->teaserManager = $teaserManager;
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(Request $request, RenderConfiguration $renderConfiguration)
    {
        if (!$renderConfiguration->hasFeature('treeNode')) {
            return;
        }

        $elementtypeId = $renderConfiguration->get('contentElement')->getElementtypeId();
        $elementtype = $this->elementSourceManager->findElementtype($elementtypeId);

        $layouts = [];
        $layoutareas = [];
        foreach ($this->elementSourceManager->findElementtypesByType('layout') as $layoutarea) {
            if (in_array($elementtype, $this->elementService->findAllowedParents($layoutarea))) {
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

        $areas = [];

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

            $teasers = $this->teaserManager->findForLayoutAreaAndTreeNodePath($layoutarea, $treeNodePath);

            $areas[$layoutarea->getUniqueId()] = [
                'title'    => $layoutarea->getTitle(),
                'uniqueId' => $layoutarea->getUniqueId(),
                'children' => $teasers
            ];

            //$areaEvent = new Brainbits_Event_Notification(new stdClass(), 'area');
            //$this->_dispatcher->dispatch($areaEvent);
        }

        $renderConfiguration
            ->addFeature('layoutarea')
            ->setVariable('teasers', $areas);

        $event = new ConfigureEvent($renderConfiguration);
        $this->dispatcher->dispatch(ElementRendererEvents::CONFIGURE_LAYOUTAREA, $event);
    }
}
