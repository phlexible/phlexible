<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Twig\Extension;

use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeContext;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Phlexible\Bundle\TreeBundle\Pattern\PatternResolver;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

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
     * @var PatternResolver
     */
    private $patternResolver;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param ContentTreeManagerInterface   $contentTreeManager
     * @param PatternResolver               $patternResolver
     * @param RequestStack                  $requestStack
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        ContentTreeManagerInterface $contentTreeManager,
        PatternResolver $patternResolver,
        RequestStack $requestStack,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        $this->contentTreeManager = $contentTreeManager;
        $this->patternResolver = $patternResolver;
        $this->requestStack = $requestStack;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('tree_node', [$this, 'treeNode']),
            new \Twig_SimpleFunction('node_granted', [$this, 'nodeGranted']),
            new \Twig_SimpleFunction('page_title', [$this, 'pageTitle']),
            new \Twig_SimpleFunction('page_title_pattern', [$this, 'pageTitlePattern']),
        ];
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
     * @param TreeNodeInterface|ContentTreeContext $node
     *
     * @return bool
     */
    public function nodeGranted($node)
    {
        /* @var $nodes TreeNodeInterface[] */

        if ($node instanceof ContentTreeContext) {
            $nodes = array($node->getNode());
        } elseif (is_array($node)) {
            $nodes = $node;
        } elseif (!$node instanceof TreeNodeInterface) {
            return false;
        } else {
            $nodes = array($node);
        }

        foreach ($nodes as $node) {
            if ($node instanceof ContentTreeContext) {
                $node = $node->getNode();
            }

            if ($this->authorizationChecker->isGranted(new Expression($node->getSecurityExpression()))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string            $name
     * @param string            $language
     * @param TreeNodeInterface $treeNode
     * @param Siteroot          $siteroot
     *
     * @return string
     */
    public function pageTitle($name = 'default', $language = null, TreeNodeInterface $treeNode = null, Siteroot $siteroot = null)
    {
        $request = $this->requestStack->getMasterRequest();

        if ($siteroot === null) {
            $siteroot = $request->attributes->get('siterootUrl')->getSiteroot();
        }

        if ($treeNode === null) {
            $treeNode = $request->get('contentDocument');
        }

        if ($language === null) {
            $language = $request->getLocale();
        }

        $title = $this->patternResolver->replace($name, $siteroot, $treeNode->getTree()->getContent($treeNode), $language);

        return $title;
    }

    /**
     * @param string            $pattern
     * @param string            $language
     * @param TreeNodeInterface $treeNode
     * @param Siteroot          $siteroot
     *
     * @return string
     */
    public function pageTitlePattern($pattern, $language = null, TreeNodeInterface $treeNode = null, Siteroot $siteroot = null)
    {
        $request = $this->requestStack->getMasterRequest();

        if ($siteroot === null) {
            $siteroot = $request->attributes->get('siterootUrl')->getSiteroot();
        }

        if ($treeNode === null) {
            $treeNode = $request->get('contentDocument');
        }

        if ($language === null) {
            $language = $request->getLocale();
        }

        $title = $this->patternResolver->replacePattern($pattern, $siteroot, $treeNode->getTree()->getContent($treeNode), $language);

        return $title;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'phlexible_tree';
    }
}
