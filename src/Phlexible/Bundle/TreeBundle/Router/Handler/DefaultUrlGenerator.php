<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Router\Handler;

use Phlexible\Bundle\SiterootBundle\Siteroot\SiterootHostnameGenerator;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

/**
 * Path generator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DefaultUrlGenerator implements UrlGeneratorInterface
{
    /**
     * @var ContentTreeManagerInterface
     */
    private $contentTreeManager;

    /**
     * @var SiterootHostnameGenerator
     */
    private $siterootHostnameGenerator;

    /**
     * @var RequestContext
     */
    protected $requestContext;

    /**
     * @param ContentTreeManagerInterface $treeManager
     * @param SiterootHostnameGenerator   $siterootHostnameGenerator
     */
    public function __construct(
        ContentTreeManagerInterface $treeManager,
        SiterootHostnameGenerator $siterootHostnameGenerator
    ) {
        $this->contentTreeManager = $treeManager;
        $this->siterootHostnameGenerator = $siterootHostnameGenerator;
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
        $path = $this->generatePath(
            $treeNode,
            $mergedParams
        );

        $url .= $basePath . $path;

        return $encode ? htmlspecialchars($url) : $url;
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

        $pathGenerator = new PathGenerator();
        $path = $pathGenerator->generatePath($node, $parameters);

        if ($path === '') {
            return $path;
        }

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
