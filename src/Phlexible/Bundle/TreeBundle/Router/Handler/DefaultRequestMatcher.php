<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Router\Handler;

use Phlexible\Bundle\SiterootBundle\Entity\Url;
use Phlexible\Bundle\SiterootBundle\Siteroot\SiterootRequestMatcher;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;

/**
 * Default request matcher
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DefaultRequestMatcher implements RequestMatcherInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ContentTreeManagerInterface
     */
    protected $contentTreeManager;

    /**
     * @var SiterootRequestMatcher
     */
    protected $siterootRequestMatcher;

    /**
     * @var array
     */
    protected $languages;

    /**
     * @var string
     */
    protected $defaultLanguage;

    /**
     * @param LoggerInterface             $logger
     * @param ContentTreeManagerInterface $treeManager
     * @param SiterootRequestMatcher      $siterootRequestMatcher
     * @param string                      $languages
     * @param string                      $defaultLanguage
     */
    public function __construct(
        LoggerInterface $logger,
        ContentTreeManagerInterface $treeManager,
        SiterootRequestMatcher $siterootRequestMatcher,
        $languages,
        $defaultLanguage)
    {
        $this->logger = $logger;
        $this->contentTreeManager = $treeManager;
        $this->siterootRequestMatcher = $siterootRequestMatcher;
        $this->languages = explode(',', $languages);
        $this->defaultLanguage = $defaultLanguage;
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
        $parameters = $this->matchPath($request);

        if ($parameters === null) {
            throw new ResourceNotFoundException();
        }

        if (isset($parameters['_route_object'])) {
            $treeNode = $parameters['_route_object'];

            $this->applyCache($treeNode, $request);
            $this->applySecurity($treeNode, $request);
        }

        return $parameters;
    }

    /**
     * @param TreeNodeInterface $treeNode
     * @param Request           $request
     */
    protected function applyCache(TreeNodeInterface $treeNode, Request $request)
    {
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
    }

    /**
     * @param TreeNodeInterface $treeNode
     * @param Request           $request
     */
    protected function applySecurity(TreeNodeInterface $treeNode, Request $request)
    {
        if ('true' !== $expression = $treeNode->getSecurityExpression()) {
            $configuration = new Security(array('expression' => $expression));
            $request->attributes->set('_security', $configuration);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function matchPath(Request $request)
    {
        $path = $request->getPathInfo();

        if ($defaults = $this->matchPreviewPath($path)) {
            $tree = $this->contentTreeManager->findByTreeId($defaults['tid']);

            $siterootUrl = $tree->getSiteroot()->getDefaultUrl();

        } else {
            $siterootUrl = $this->matchSiterootUrl($request);

            if (!($defaults = $this->matchSiterootPath($siterootUrl, $path))) {
                return null;
            }

            $tree = $this->contentTreeManager->find($siterootUrl->getSiteroot()->getId());
        }

        $defaults['siterootUrl'] = $siterootUrl;

        if (empty($defaults['language'])) {
            $defaults['language'] = $this->findLanguage();
        }

        if (!empty($defaults['language'])) {
            $request->setLocale($defaults['language']);
            $defaults['_locale'] = $defaults['language'];
            $tree->setLanguage($defaults['language']);
        }

        if (empty($defaults['tid'])) {
            return null;
        }

        $treeNode = $tree->get($defaults['tid']);
        if (!$treeNode) {
            return null;
        }

        $defaults['_route'] = $path;
        $defaults['_route_object'] = $treeNode;
        $defaults['_content'] = $treeNode;
        $defaults['_controller'] = 'PhlexibleFrontendBundle:Online:index';

        return $defaults;
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
     * @param string $path
     *
     * @return array|null
     */
    protected function matchPreviewPath($path)
    {
        if (!preg_match('#^/admin/preview/(\w\w)/(\d+)$#', $path, $match)) {
            return null;
        }

        return array(
            '_preview' => true,
            'language' => $match[1],
            'tid' => $match[2]
        );
    }

    /**
     * @param Request $request
     *
     * @return Url|null
     */
    protected function matchSiterootUrl(Request $request)
    {
        $siteroot = $this->siterootRequestMatcher->matchRequest($request);
        if (!$siteroot) {
            return null;
        }

        return $siteroot->getDefaultUrl();
    }

    /**
     * @param Url    $siterootUrl
     * @param string $path
     *
     * @return array|null
     */
    protected function matchSiterootPath(Url $siterootUrl, $path)
    {
        if (!strlen($path) || $path === '/') {
            return array(
                'language' => $siterootUrl->getLanguage(),
                'tid' => $siterootUrl->getTarget()
            );
        } elseif (!preg_match('#^/(\w\w)/(.+)\.(\d+)\.html#', $path, $match)) {
            return array(
                'language' => $match[1],
                'tid' => $match[2]
            );
        }

        return null;
    }
}
