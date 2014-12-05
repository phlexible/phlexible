<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Configurator;

use Phlexible\Bundle\AccessControlBundle\Rights as ContentRightsManager;
use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementRendererBundle\Configurator\ConfiguratorInterface;
use Phlexible\Bundle\ElementRendererBundle\Configurator\Configuration;
use Phlexible\Bundle\ElementRendererBundle\ElementRendererEvents;
use Phlexible\Bundle\ElementRendererBundle\Event\ConfigureEvent;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeContext;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Element configurator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeNodeConfigurator implements ConfiguratorInterface
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
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param LoggerInterface          $logger
     * @param ElementService           $elementService
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger,
        ElementService $elementService,
        SecurityContextInterface $securityContext)
    {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
        $this->elementService = $elementService;
        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(Request $request, Configuration $renderConfiguration)
    {
        if (!$request->attributes->has('contentDocument') || !$request->attributes->get('contentDocument') instanceof TreeNodeInterface) {
            return;
        }

        /* @var $treeNode TreeNodeInterface */
        $treeNode = $originalTreeNode = $request->attributes->get('contentDocument');
        $tree = $treeNode->getTree();

        $eid = $treeNode->getTypeId();

        if (0) {
            // || $renderRequest->getVersionStrategy() === 'latest')
            if (!$this->securityContext->isGranted('VIEW', $treeNode)) {
                $this->logger->debug('403 Forbidden du to missing VIEW content right');

                throw new \Makeweb_Renderers_Exception('Forbidden', 403);
            }
        }

        if ($treeNode !== $originalTreeNode) {
            $this->logger->debug('Switching to TID ' . $treeNode->getId());

            $renderRequest->setTreeNode($treeNode);
            $renderRequest->setVersion($elementVersion->getVersion());
        }

        // if available use delegation for showing element somewhere else in navigation
        if ($request->attributes->has('delegateTreeId')) {
            $delegateTreeNode = $tree->getNode($request->attributes->get('delegateTreeId'));
        }

        $renderConfiguration
            ->addFeature('treeNode')
            ->setVariable('treeNode', $treeNode)
            ->setVariable('treeContext', new ContentTreeContext($treeNode))
            ->addFeature('eid')
            ->set('eid', $treeNode->getTypeId())
            ->set('version', 1)//$tree->getPublishedVersion($treeNode, 'de'))
            ->set('language', 'de');

        if ($treeNode->getTemplate()) {
            $renderConfiguration
                ->addFeature('template')
                ->setVariable('template', $treeNode->getTemplate());
        }

        $renderConfiguration
            ->setVariable('siteroot', $request->attributes->get('siterootUrl')->getSiteroot())
            ->setVariable('specialTids', $this->createSpecialTids($renderConfiguration));

        $event = new ConfigureEvent($renderConfiguration);
        $this->dispatcher->dispatch(ElementRendererEvents::CONFIGURE_TREE_NODE, $event);

        return true;
    }

    /**
     * @param Configuration $renderConfiguration
     * @param string              $language
     *
     * @return array
     */
    private function createSpecialTids(Configuration $renderConfiguration)
    {
        $language = $renderConfiguration->get('language');

        $specialTids = [];

        foreach ($renderConfiguration->get('request')->attributes->get('siterootUrl')->getSiteroot()->getSpecialTids(null) as $specialTid) {
            $specialTids[$specialTid['name']] = $specialTid['treeId'];
        }

        foreach ($renderConfiguration->get('request')->attributes->get('siterootUrl')->getSiteroot()->getSpecialTids($language) as $specialTid) {
            $specialTids[$specialTid['name']] = $specialTid['treeId'];
        }

        return $specialTids;
    }
}
