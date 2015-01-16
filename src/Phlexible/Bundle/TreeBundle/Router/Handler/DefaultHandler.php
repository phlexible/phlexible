<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Router\Handler;

use Phlexible\Bundle\SiterootBundle\Entity\Url;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeInterface;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeManagerInterface;
use Phlexible\Bundle\TreeBundle\Exception\NoSiterootUrlFoundException;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\RequestContext;

/**
 * Default handler
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DefaultHandler implements RequestMatcherInterface, UrlGeneratorInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ContentTreeManagerInterface
     */
    private $contentTreeManager;

    /**
     * @var array
     */
    private $languages;

    /**
     * @var string
     */
    private $defaultLanguage;

    /**
     * @var RequestContext
     */
    private $requestContext;

    /**
     * @param LoggerInterface             $logger
     * @param ContentTreeManagerInterface $treeManager
     * @param string                      $languages
     * @param string                      $defaultLanguage
     */
    public function __construct(
        LoggerInterface $logger,
        ContentTreeManagerInterface $treeManager,
        $languages,
        $defaultLanguage)
    {
        $this->logger = $logger;
        $this->contentTreeManager = $treeManager;
        $this->languages = explode(',', $languages);
        $this->defaultLanguage = $defaultLanguage;
    }

    /**
     * {@inheritdoc}
     */
    public function setContext(RequestContext $requestContext)
    {
        $this->requestContext = $requestContext;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContext()
    {
        return $this->requestContext;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH)
    {
        /* @var $treeNode TreeNodeInterface */
        $treeNode = $name;
        $language = 'de';//$parameters['language'];
        $encode = false;
        /*
        TreeNode $treeNode,
        $language,
        $fragment = '',
        $encode = false
        */

        $url = '';

        if (0 && $referenceType === self::ABSOLUTE_URL) {
            $scheme = $this->requestContext->getScheme();
            if (!$scheme || $scheme === 'http') {
                $scheme = $treeNode->getAttribute('https', 'http');
            }

            $hostname = $this->generateHostname($treeNode, $language);

            $port = '';
            if ($scheme === 'http' && $this->requestContext->getHttpPort() !== 80) {
                $port = ':' . $this->requestContext->getHttpPort();
            }
            if ($scheme === 'https' && $this->requestContext->getHttpsPort() !== 443) {
                $port = ':' . $this->requestContext->getHttpsPort();
            }

            $url .= $scheme . '://' . $hostname . $port;
        }

        $basePath = $this->requestContext->getBaseUrl();

        $path = $this->generatePath($treeNode, $language);

        $query = '';
        if (count($parameters)) {
            $query = '?' . http_build_query($parameters, '', '&');
        }

        $fragment = '';

        $url .= $basePath . $path . $query . $fragment;

        return $encode ? htmlspecialchars($url) : $url;
    }

    /**
     * {@inheritdoc}
     */
    public function match($pathinfo)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function matchRequest(Request $request)
    {
        // remove pathPrefix (debug, preview), strip trailing slash
        $tree = $this->findTree($request);

        if (null === $tree) {
            $msg = 'No matching siteroot url found, and no fallback siteroot url provided.';
            throw new NoSiterootUrlFoundException($msg);
        }

        $parameters = $this->matchIdentifiers($request, $tree);

        if (0 && !$siterootUrl->isDefault()) {
            $siterootUrl = $siterootUrl->getSiteroot()->getDefaultUrl($request->attributes->get('language'));
            // forward?
        }

        //$request->attributes->set('siterootUrl', $siterootUrl);

        return $parameters;

        return [
            'siterootUrl' => $siterootUrl,
            'identifiers' => $identifiers,
            'parameters'  => $parameters,
        ];
    }

    /**
     * Match siteroot URL.
     *
     * @param Request $request
     *
     * @return int|null
     */
    protected function findTree(Request $request)
    {
        $default = null;
        foreach ($this->contentTreeManager->findAll() as $tree) {
            foreach ($tree->getUrls() as $siterootUrl) {
                if ($siterootUrl->getHostname() === $request->getHttpHost()) {
                    $request->attributes->set('siterootUrl', $siterootUrl);

                    return $tree;
                }
                if ($tree->isDefaultSiteroot()) {
                    $default = ['tree' => $tree, 'siterootUrl' => $siterootUrl];
                }
            }
        }

        if ($default) {
            $request->attributes->set('siterootUrl', $default['siterootUrl']);

            return $default['tree'];
        }

        return null;
    }

    /**
     * Match identifieres (tid, language, ...)
     *
     * @param Request              $request
     * @param ContentTreeInterface $tree
     *
     * @return array
     */
    protected function matchIdentifiers(Request $request, ContentTreeInterface $tree)
    {
        $match = [];
        $path = $request->getPathInfo();

        /* @var $siterootUrl Url */
        $siterootUrl = $request->attributes->get('siterootUrl');

        $attributes = [];

        if (!strlen($path)) {
            // no path, use siteroot defaults
            $language = $siterootUrl->getLanguage();
            $tid = $siterootUrl->getTarget();

            $this->logger->debug('Using TID from siteroot url target: ' . $tid . ':' . $language);
        } elseif (preg_match('#^/(\w\w)/(.+)\.(\d+)\.html#', $path, $match)) {
            // match found
            $language = $match[1];
            //$path     = $match[2];
            $tid = $match[3];
        } elseif (preg_match('#^/preview/(\w\w)/(.+)\.(\d+)\.html#', $path, $match)) {
            // match found
            $language = $match[1];
            //$path     = $match[2];
            $tid = $match[3];
        } else {
            $language = null;
            $tid = null;
            $language = $siterootUrl->getLanguage();
            $tid = $siterootUrl->getTarget();
        }

        if ($language === null) {
            if (function_exists('http_negotiate_language')) {
                array_unshift($this->languages, $this->defaultLanguage);

                $language = http_negotiate_language($this->languages);
                $this->logger->debug('Using negotiated language: ' . $language);
            } else {
                $language = $this->defaultLanguage;
                $this->logger->debug('Using default language: ' . $language);
            }
        }

        if ($tid) {
            $request->attributes->set('tid', $tid);

            $tree->setLanguage($language);
            $treeNode = $tree->get($tid);
            /*
            if ($siterootUrl->getSiteroot()->getId() === $tree->getSiteRootId()) {
                // only set on valid siteroot
                $treeNode = $tree->get($tid);
            }
            */

            $attributes['_route'] = $path;
            $attributes['_route_object'] = $treeNode;
            $attributes['_content'] = $treeNode;
            $attributes['_controller'] = 'PhlexibleFrontendBundle:Online:index';
        }

        $request->setLocale($language);
        $request->attributes->set('_locale', $language);

        return $attributes;
    }

    /**
     * Match parameters
     *
     * @param Request $request
     *
     * @return array
     */
    protected function matchParameters(Request $request)
    {
        return $request->query->all();
    }

    /**
     * Generate hostname
     *
     * @param \Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface $node
     *
     * @return string
     */
    protected function generateHostname(TreeNodeInterface $node)
    {
        $siteroot = $this->siterootRepository->find($node->getSiteRootId());
        $siterootUrl = $siteroot->getDefaultUrl();

        return $siterootUrl->getHostname();
    }

    /**
     * Generate path
     *
     * @param TreeNodeInterface $node
     * @param string            $language
     *
     * @return string
     */
    protected function generatePath(TreeNodeInterface $node, $language)
    {
        if ($this->requestContext->getParameter('preview')) {
            return $this->generatePreviewPath($node, $language);
        }

        $tree = $node->getTree();

        // we reverse the order to determine if this leaf is no full element
        // if the is the case we don't have to continue, only full elements
        // have paths
        $pathNodes = array_reverse($tree->getPath($node));

        $parts = [];

        foreach ($pathNodes as $pathNode) {
            if ($tree->isViewable($pathNode)) {
                $parts[] = $pathNode->getSlug($language);
            }
        }

        if (!count($parts)) {
            if (!count($pathNodes)) {
                return '';
            }

            $current = $pathNodes[0];
            $parts[] = $current->getSlug($language);
        }

        $path = '/' . implode('/', array_reverse($parts));

        /*
        // transliterate to ascii
        $path = $this->_transliterate($path);
        // to lowercase
        $path = mb_strtolower($path, 'UTF-8');
        // replace non ascii chars with underscore
        $path = preg_replace('#[^a-z0-9_/]+#', '_', $path);
        // replace duplicate underscores with single underscore
        $path = preg_replace('#_{2,}#', '_', $path);
        // remove leading underscores in path fragments
        $path = preg_replace('#(.*)/_+(.*)$#', '$1/$2', $path);
        // remove trailing underscores in path fragments
        $path = preg_replace('#(.*)_+/(.*)$#', '$1/$2', $path);
        // remove trailing underscores
        $path = preg_replace('#_+$#', '', $path);
        */

        // add language
        $path = '/' . $language . $path;

        /*
        if ($this->hasContext())
        {
            $country = $this->_context->getCountry();

            if (Makeweb_Elements_Context::NO_COUNTRY === $country)
            {
                $container = MWF_Registry::getContainer();

                $country = $container->getParam(':phlexible_element.context.default_country');

                if (!strlen($country))
                {
                    $country = Makeweb_Elements_Context::GLOBAL_COUNTRY;
                }
            }

            $cleartext = '/' . $country . $cleartext;
        }
        */

        // add tid and postfix
        $path .= '.' . $node->getId() . '.html';

        return $path;
    }

    protected function generatePreviewPath(TreeNodeInterface $node, $language)
    {
        return "/admin/frontend/preview/$language/{$node->getId()}";
    }
}
