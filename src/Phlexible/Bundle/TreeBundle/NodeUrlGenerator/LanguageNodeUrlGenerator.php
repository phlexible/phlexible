<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\NodeUrlGenerator;

use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeManagerInterface;
use Phlexible\Bundle\TreeBundle\Entity\TreeNode;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Language node url generator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LanguageNodeUrlGenerator implements NodeUrlGeneratorInterface
{
    /**
     * @var ContentTreeManagerInterface
     */
    private $treeManager;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param ContentTreeManagerInterface $treeManager
     * @param RouterInterface             $router
     */
    public function __construct(ContentTreeManagerInterface $treeManager, RouterInterface $router)
    {
        $this->router = $router;
        $this->treeManager = $treeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function generatePreviewUrl(TreeNode $node, $language)
    {
        $contentNode = $this->treeManager->findByTreeId($node->getId())->get($node->getId());

        return $this->router->generate($contentNode, ['_preview' => true, '_locale' => $language], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * {@inheritdoc}
     */
    public function generateOnlineUrl(TreeNode $node, $language)
    {
        $contentNode = $this->treeManager->findByTreeId($node->getId())->get($node->getId());

        return $this->router->generate($contentNode, ['_locale' => $language], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
