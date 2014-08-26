<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementRendererBundle\RenderConfigurator;

use Phlexible\Bundle\AccessControlBundle\Rights as ContentRightsManager;
use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementRendererBundle\RenderConfiguration;
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
    public function configure(Request $request, RenderConfiguration $renderConfiguration)
    {
        if (!$request->attributes->has('contentDocument') || !$request->attributes->get('contentDocument') instanceof TreeNodeInterface) {
            return;
        }

        // Before Init Element Event
        /*
        $beforeEvent = new \Makeweb_Renderers_Event_BeforeInitElement($this);
        if (!$this->dispatcher->dispatch($beforeEvent))
        {
            return false;
        }
        */

        /* Context */
        if ($request->attributes->has('country')) {
            $context = $request->attributes->get('country');

            if (!$context->isRelevantForTid($parameters->getId(), $request->attributes->get('availableLanguages'))) {
                $msg = 'TID "' . $parameters->getId() . '": '
                    . 'Language "' . $request->attributes->get('language') . '"'
                    . ' not available in country "' . $context->getCountry() . '".';

                $this->logger->debug($msg);

                throw new \Exception($msg);

            }
        }

        /* Context */

        /* @var $parameters TreeNodeInterface */
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

        /*
        // SSL redirect
        $forceProtocol         = $container->getParameter('frontend.request.protocol');
        $protocolBusinesslogic = $container->getParameter('frontend.request.protocol_businesslogic');
        $protocolStayssl       = $container->getParameter('frontend.request.stayssl');

        if ($forceProtocol === 'http')
        {
            $sslNeeded      = false;
            $protocolSwitch = false;

        }
        else if ($forceProtocol === 'https')
        {
            $sslNeeded      = false;
            $protocolSwitch = false;

            $this->_request->setSsl(true);
        }
        else
        {
            $sslNeeded = $elementNode->isHttps($elementVersion->getVersion());

            // set protocol for businesslogic processes
            if ($protocolBusinesslogic === 'https' || $protocolBusinesslogic === 'http')
            {
                $elementUniqueId    = $elementType->getUniqueId();
                if ($elementUniqueId === 'businesslogic' || $elementUniqueId === 'customformlogic')
                {
                    if ($protocolBusinesslogic == 'https')
                    {
                        $sslNeeded = true;
                    }
                    else
                    {
                        $sslNeeded = false;
                    }
                }
            }

            // set protocol if stayssl is requested
            if ($protocolStayssl === 'true')
            {
                $sslRequest = $this->_request->isSsl();
                $cookieSsl  = isset($_COOKIE['stayssl']) ? $_COOKIE['stayssl'] : null;

                if (!$sslRequest && $cookieSsl)
                {
                    // switch to ssl
                    $sslNeeded = true;
                }
                else if (($sslRequest || $sslNeeded) && !$cookieSsl)
                {
                    //set cookie for ssl
                    setcookie('stayssl', true, time()+3600, '/');
                    $_COOKIE['stayssl'] = true;
                }
                else
                {
                    $sslNeeded = $sslRequest; // no protocolswitch
                }
            }
            $protocolSwitch = $sslNeeded !== $this->_request->isSsl();
        }

        if ($protocolSwitch)
        {
            $this->_debugLine('Need protocol switch', 'notice');
            $this->_request->setSsl($sslNeeded);

            // create redirect link
            $redirectLink = Makeweb_Navigations_Link::create(	$this->_request,
                $this->_request->getTid(),
                $this->_request->getLanguage(),
                $this->_request->getParams()
            );
            // change protocol if needed
            if($sslNeeded)
            {
                $redirectLink = str_replace('http://', 'https://', $redirectLink);
            }
            else
            {
                $redirectLink = str_replace('https://', 'http://', $redirectLink);
            }

            $this->_response
                ->setHttpResponseCode(301)
                ->setHeader('Location', $redirectLink);

            return false;
        }
        */

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
            ->set('treeNode', $treeNode)
            ->set('treeContext', new ContentTreeContext($treeNode))
            ->addFeature('eid')
            ->set('eid', $treeNode->getTypeId())
            ->set('version', 1)//$tree->getPublishedVersion($treeNode, 'de'))
            ->set('language', 'de');

        // Init Element Event
        /*
        $event = new \Makeweb_Renderers_Event_InitElement($this);
        $this->dispatcher->dispatch($event);
        */

        return true;
    }
}
