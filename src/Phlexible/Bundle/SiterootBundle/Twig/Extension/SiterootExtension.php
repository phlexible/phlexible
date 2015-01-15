<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\Twig\Extension;

use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;
use Phlexible\Bundle\SiterootBundle\Siteroot\PatternResolver;
use Phlexible\Bundle\SiterootBundle\Siteroot\TitleResolver;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Twig siteroot extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiterootExtension extends \Twig_Extension
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var SiterootManagerInterface
     */
    private $siterootManager;

    /**
     * @var PatternResolver
     */
    private $patternResolver;

    /**
     * @param RequestStack             $requestStack
     * @param SiterootManagerInterface $siterootManager
     * @param PatternResolver          $patternResolver
     */
    public function __construct(
        RequestStack $requestStack,
        SiterootManagerInterface $siterootManager,
        PatternResolver $patternResolver)
    {
        $this->requestStack = $requestStack;
        $this->siterootManager = $siterootManager;
        $this->patternResolver = $patternResolver;
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
     * @param string            $name
     * @param string            $language
     *
     * @return string
     */
    public function pageTitle(TreeNodeInterface $treeNode = null, $name = 'default', $language = null)
    {
        $request = $this->requestStack->getMasterRequest();

        if ($treeNode === null) {
            $treeNode = $request->get('contentDocument');
        }

        if ($language === null) {
            $language = $request->getLocale();
        }

        $siteroot = $this->siterootManager->find($treeNode->getTree()->getSiterootId());
        $pattern = $siteroot->getPattern($name);

        $title = $this->patternResolver->replace($siteroot, $treeNode->getTree()->getContent($treeNode), $language, $pattern);

        return $title;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'phlexible_siteroot';
    }
}
