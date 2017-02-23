<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Twig\Extension;

use Phlexible\Bundle\ElementBundle\ContentElement\ContentElement;
use Phlexible\Bundle\ElementBundle\ContentElement\ContentElementLoader;
use Phlexible\Bundle\TeaserBundle\ContentTeaser\ContentTeaser;
use Phlexible\Bundle\TeaserBundle\ContentTeaser\DelegatingContentTeaserManager;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeContext;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Twig element extension.
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
     * @var DelegatingContentTeaserManager
     */
    private $teaserManager;

    /**
     * @param ContentElementLoader           $contentElementLoader
     * @param DelegatingContentTeaserManager $teaserManager
     * @param RequestStack                   $requestStack
     */
    public function __construct(ContentElementLoader $contentElementLoader, DelegatingContentTeaserManager $teaserManager, RequestStack $requestStack)
    {
        $this->contentElementLoader = $contentElementLoader;
        $this->teaserManager = $teaserManager;
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
     * @param ContentTeaser|TreeNodeInterface|ContentTreeContext|int $eid
     *
     * @return ContentElement|null
     */
    public function element($eid)
    {
        $language = $this->requestStack->getCurrentRequest()->getLocale();

        if ($eid instanceof ContentTeaser) {
            $teaser = $eid;
            $eid = $teaser->getTypeId();
            $version = $teaser->getVersion();
        } elseif ($eid instanceof TreeNodeInterface) {
            $node = $eid;
            $eid = $node->getTypeId();
            $version = $node->getTree()->getVersion($node, $language);
        } elseif ($eid instanceof ContentTreeContext) {
            $node = $eid->getNode();
            $eid = $node->getTypeId();
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
