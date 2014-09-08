<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TwigRendererBundle\Extension;

use Phlexible\Bundle\ElementBundle\Model\ElementStructureValue;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeContext;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Twig url extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UrlExtension extends \Twig_Extension
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ContentTreeManagerInterface
     */
    private $contentTreeManager;

    /**
     * @param RouterInterface             $router
     * @param ContentTreeManagerInterface $contentTreeManager
     */
    public function __construct(RouterInterface $router, ContentTreeManagerInterface $contentTreeManager)
    {
        $this->router = $router;
        $this->contentTreeManager = $contentTreeManager;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('url', array($this, 'url')),
            new \Twig_SimpleFunction('treeNode', array($this, 'treeNode')),
        );
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function url($path)
    {
        if ($path instanceof TreeNodeInterface) {
            return $this->router->generate($path);
        } elseif ($path instanceof ContentTreeContext) {
            return $this->router->generate($path->getNode());
        } elseif ($path instanceof ElementStructureValue) {
            if ($path->getType() === 'link') {
                $link = $path->getValue();
                if ($link['type'] === 'internal' || $link['type'] === 'intrasiteroot') {
                    $tree = $this->contentTreeManager->findByTreeId($link['tid']);
                    $node = $tree->get($link['tid']);

                    return $this->router->generate($node);
                } elseif ($link['type'] === 'external') {
                    return $link['url'];
                } elseif ($link['type'] === 'mailto') {
                    return 'mailto:' . $link['recipient'];
                }
            }
        }

        return '';
    }

    /**
     * @param string $treeId
     *
     * @return ContentTreeContext
     */
    public function treeNode($treeId)
    {
        $node = $this->contentTreeManager->findByTreeId($treeId);

        return new ContentTreeContext($node);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'url';
    }
}