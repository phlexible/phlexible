<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TwigRendererBundle\Twig\Extension;

use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeContext;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeManagerInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Twig tree extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeExtension extends \Twig_Extension
{
    /**
     * @var ContentTreeManagerInterface
     */
    private $contentTreeManager;

    /**
     * @param ContentTreeManagerInterface $contentTreeManager
     */
    public function __construct(ContentTreeManagerInterface $contentTreeManager)
    {
        $this->contentTreeManager = $contentTreeManager;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('treeNode', array($this, 'treeNode')),
        );
    }

    /**
     * @param string $treeId
     *
     * @return ContentTreeContext
     */
    public function treeNode($treeId)
    {
        $tree = $this->contentTreeManager->findByTreeId($treeId);
        $treeNode = $tree->get($treeId);

        return new ContentTreeContext($treeNode);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'phlexible_tree';
    }
}
