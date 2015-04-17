<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Twig\Extension;

use Phlexible\Bundle\ElementBundle\ContentElement\ContentElement;
use Phlexible\Bundle\ElementBundle\ContentElement\ContentElementLoader;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeContext;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Twig element extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementExtension extends \Twig_Extension
{
    /**
     * @var ContentElementLoader
     */
    private $contentElementLoader;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param ContentElementLoader $contentElementLoader
     * @param RequestStack         $requestStack
     */
    public function __construct(ContentElementLoader $contentElementLoader, RequestStack $requestStack)
    {
        $this->contentElementLoader = $contentElementLoader;
        $this->requestStack = $requestStack;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('element', [$this, 'element']),
        ];
    }

    /**
     * @param TreeNodeInterface|ContentTreeContext|int $eid
     *
     * @return ContentElement|null
     */
    public function element($eid)
    {
        if (is_int($eid)) {
            $language = $this->requestStack->getCurrentRequest()->getLocale();
            $version = 1;
        } elseif ($eid instanceof TreeNodeInterface) {
            $node = $eid;
            $eid = $node->getTypeId();
            $language = $this->requestStack->getCurrentRequest()->getLocale();
            $version = $node->getTree()->getVersion($node, $language);
        } elseif ($eid instanceof ContentTreeContext) {
            $node = $eid->getNode();
            $eid = $node->getTypeId();
            $language = $this->requestStack->getCurrentRequest()->getLocale();
            $version = $node->getTree()->getVersion($node, $language);
        } else {
            return null;
        }

        return $this->contentElementLoader->load($eid, $version, $language);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'phlexible_element';
    }
}
