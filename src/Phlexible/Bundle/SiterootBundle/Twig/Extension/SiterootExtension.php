<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\Twig\Extension;

use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;
use Phlexible\Bundle\SiterootBundle\Siteroot\TitleResolver;
use Phlexible\Bundle\TreeBundle\Model\TreeContext;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;

/**
 * Twig siteroot extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiterootExtension extends \Twig_Extension
{
    /**
     * @var SiterootManagerInterface
     */
    private $siterootManager;

    /**
     * @var TitleResolver
     */
    private $titleResolver;

    /**
     * @param SiterootManagerInterface $siterootManager
     * @param TitleResolver            $titleResolver
     */
    public function __construct(SiterootManagerInterface $siterootManager, TitleResolver $titleResolver)
    {
        $this->siterootManager = $siterootManager;
        $this->titleResolver = $titleResolver;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('pageTitle', [$this, 'pageTitle']),
        ];
    }

    /**
     * @param TreeNodeInterface $treeNode
     *
     * @return string
     */
    public function pageTitle(TreeNodeInterface $treeNode)
    {
        $siteroot = $this->siterootManager->find($treeNode->getTree()->getSiterootId());

        $title = $this->titleResolver->replace($siteroot, $treeNode->getTree()->getContentDocument(), 'de');

        return new TreeContext($treeNode);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'phlexible_tree';
    }
}
