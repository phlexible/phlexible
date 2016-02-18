<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Router\Handler;

use Phlexible\Bundle\SiterootBundle\Entity\Url;
use Phlexible\Bundle\SiterootBundle\Siteroot\SiterootHostnameGenerator;
use Phlexible\Bundle\SiterootBundle\Siteroot\SiterootRequestMatcher;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeInterface;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeManagerInterface;
use Phlexible\Bundle\TreeBundle\Exception\NoSiterootUrlFoundException;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
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
     * @var SiterootRequestMatcher
     */
    private $siterootRequestMatcher;

    /**
     * @var SiterootHostnameGenerator
     */
    private $siterootHostnameGenerator;

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
     * @param SiterootRequestMatcher      $siterootRequestMatcher
     * @param SiterootHostnameGenerator   $siterootHostnameGenerator
     * @param string                      $languages
     * @param string                      $defaultLanguage
     */
    public function __construct(
        LoggerInterface $logger,
        ContentTreeManagerInterface $treeManager,
        SiterootRequestMatcher $siterootRequestMatcher,
        SiterootHostnameGenerator $siterootHostnameGenerator,
        $languages,
        $defaultLanguage)
    {
        $this->logger = $logger;
        $this->contentTreeManager = $treeManager;
        $this->siterootRequestMatcher = $siterootRequestMatcher;
        $this->siterootHostnameGenerator = $siterootHostnameGenerator;
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
        $encode = false;

        $mergedParams = array_replace($this->requestContext->getParameters(), $parameters);

        $url = '';

        if ($referenceType === self::ABSOLUTE_URL) {
            $scheme = $this->requestContext->getScheme();
            if (!$scheme || $scheme === 'http') {
                $scheme = $treeNode->getAttribute('https', 'http');
            }

            $siteroot = $this->contentTreeManager->findByTreeId($treeNode->getId())->getSiteroot();

            $hostname = $this->siterootHostnameGenerator->generate($siteroot, $mergedParams['_locale']);

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

        $url .= $basePath . $this->generatePath($treeNode, $mergedParams);

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
        $parameters = $this->matchIdentifiers($request);

        if ($parameters === null) {
            throw new ResourceNotFoundException("bla");
        }

        /*
        if (0 && !$siterootUrl->isDefault()) {
            $siterootUrl = $siterootUrl->getSiteroot()->getDefaultUrl($request->getLocale());
            // forward?
        }
        */

        //$request->attributes->set('siterootUrl', $siterootUrl);

        if (isset($parameters['_route_object'])) {
            $treeNode = $parameters['_route_object'];
            /* @var $treeNode TreeNodeInterface */
            if ($cache = $treeNode->getAttribute('cache')) {
                $configuration = new Cache(array());

                if (!empty($cache['ETag'])) {
                    $configuration->setETag($cache['ETag']);
                }
                if (!empty($cache['lastModified'])) {
                    $configuration->setLastModified($cache['lastModified']);
                }
                if (!empty($cache['expires'])) {
                    $configuration->setExpires($cache['expires']);
                }
                if (!empty($cache['public'])) {
                    $configuration->setPublic($cache['public']);
                }
                if (!empty($cache['maxage'])) {
                    $configuration->setMaxAge($cache['maxage']);
                }
                if (!empty($cache['smaxage'])) {
                    $configuration->setSMaxAge($cache['smaxage']);
                }
                if (!empty($cache['vary'])) {
                    $configuration->setVary($cache['vary']);
                }

                $request->attributes->set('_cache', $configuration);
            }

            if ('true' !== $expression = $treeNode->getSecurityExpression()) {
                $configuration = new Security(array('expression' => $expression));
                $request->attributes->set('_security', $configuration);
            }
        }

        return $parameters;
    }

    /**
     * Match identifieres (tid, language, ...)
     *
     * @param Request $request
     *
     * @return array
     */
    protected function matchIdentifiers(Request $request)
    {
        $match = [];
        $path = $request->getPathInfo();
        $language = null;
        $tid = null;

        $attributes = [];

        if (preg_match('#^/admin/preview/(\w\w)/(\d+)$#', $path, $match)) {
            // match found
            $language = $match[1];
            $tid      = $match[2];
            $request->attributes->set('_preview', true);

            $tree = $this->contentTreeManager->findByTreeId($tid);
            $siterootUrl = $tree->getSiteroot()->getDefaultUrl();
            $request->attributes->set('siterootUrl', $siterootUrl);
        } else {
            $siteroot = $this->siterootRequestMatcher->matchRequest($request);
            if (!$siteroot) {
                return null;
            }
            $siterootUrl = $siteroot->getDefaultUrl();
            $request->attributes->set('siterootUrl', $siterootUrl);

            $tree = $this->contentTreeManager->find($siteroot->getId());

            /* @var $siterootUrl Url */
            $siterootUrl = $request->attributes->get('siterootUrl');

            if (!strlen($path) || $path === '/') {
                $language = $siterootUrl->getLanguage();
                $tid = $siterootUrl->getTarget();
            } elseif (preg_match('#^/(\w\w)/(.+)\.(\d+)\.html#', $path, $match)) {
                // match found
                $language = $match[1];
                $tid = $match[3];
            }
        }

        if ($language === null) {
            $language = $this->findLanguage();
        }

        if ($language) {
            $request->setLocale($language);
            $request->attributes->set('_locale', $language);
        }

        if (!$tid) {
            return null;
        }

        $request->attributes->set('tid', $tid);

        $tree->setLanguage($language);
        $treeNode = $tree->get($tid);
        if (!$treeNode) {
            return null;
        }

        $attributes['_route'] = $path;
        $attributes['_route_object'] = $treeNode;
        $attributes['_content'] = $treeNode;
        $attributes['_controller'] = 'PhlexibleFrontendBundle:Online:index';

        return $attributes;
    }

    /**
     * @return string
     */
    protected function findLanguage()
    {
        if (function_exists('http_negotiate_language')) {
            array_unshift($this->languages, $this->defaultLanguage);

            $language = http_negotiate_language($this->languages);
            $this->logger->debug('Using negotiated language: ' . $language);
        } else {
            $language = $this->defaultLanguage;
            $this->logger->debug('Using default language: ' . $language);
        }

        return $language;
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
     * Generate path
     *
     * @param TreeNodeInterface $node
     * @param array             $parameters
     *
     * @return string
     */
    protected function generatePath(TreeNodeInterface $node, $parameters)
    {
        if ($this->requestContext->getParameter('_preview') || isset($parameters['_preview'])) {
            return $this->generatePreviewPath($node, $parameters);
        }

        $tree = $node->getTree();

        // we reverse the order to determine if this leaf is no full element
        // if the is the case we don't have to continue, only full elements
        // have paths
        $pathNodes = array_reverse($tree->getPath($node));

        $parts = [];

        foreach ($pathNodes as $pathNode) {
            if ($tree->isViewable($pathNode)) {
                $part = $pathNode->getSlug($parameters['_locale']);
                if ($part) {
                    $parts[] = $part;
                }
            }
        }

        if (!count($parts)) {
            if (!count($pathNodes)) {
                return '';
            }

            $current = $pathNodes[0];
            $part = $current->getSlug($parameters['_locale']);
            if ($part) {
                $parts[] = $part;
            }
        }

        $path = '/' . implode('/', array_reverse($parts));

        $path = $this->generatePathPrefix($path, $node, $parameters);

        $path = $this->generatePathSuffix($path, $node, $parameters);

        $path = $this->generateQuery($path, $node, $parameters);
        return $path;
    }

    /**
     * @param string            $path
     * @param TreeNodeInterface $node
     * @param array             $parameters
     *
     * @return string
     */
    protected function generatePathPrefix($path, TreeNodeInterface $node, $parameters)
    {
        return '/' . $parameters['_locale'] . $path;
    }

    /**
     * @param string            $path
     * @param TreeNodeInterface $node
     * @param array             $parameters
     *
     * @return string
     */
    protected function generatePathSuffix($path, TreeNodeInterface $node, $parameters)
    {
        return $path . '.' . $node->getId() . '.html';
    }

    /**
     * @param string            $path
     * @param TreeNodeInterface $node
     * @param array             $parameters
     *
     * @return string
     */
    protected function generateQuery($path, TreeNodeInterface $node, $parameters)
    {
        unset($parameters['_locale']);
        unset($parameters['_preview']);

        if (count($parameters)) {
            $path .= '?' . http_build_query($parameters, '', '&');
        }

        return $path;
    }

    /**
     * @param TreeNodeInterface $node
     * @param array             $parameters
     *
     * @return string
     */
    protected function generatePreviewPath(TreeNodeInterface $node, array $parameters)
    {
        $locale = $parameters['_locale'];
        unset($parameters['_locale']);

        unset($parameters['_preview']);

        $query = '';
        if (count($parameters)) {
            $query = '?' . http_build_query($parameters);
        }

        return "/admin/preview/{$locale}/{$node->getId()}" . $query;
    }
}
