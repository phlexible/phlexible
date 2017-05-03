<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\ElementLink;

use Phlexible\Bundle\ElementBundle\ElementLink\LinkTransformer\DelegatingLinkTransformer;
use Phlexible\Bundle\ElementBundle\Entity\ElementLink;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\ElementBundle\Model\ElementLinkManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;

/**
 * Element link fetcher.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementLinkFetcher
{
    /**
     * @var DelegatingLinkTransformer
     */
    private $linkTransformer;

    /**
     * @var ElementLinkManagerInterface
     */
    private $linkManager;

    /**
     * @param DelegatingLinkTransformer   $linkTransformer
     * @param ElementLinkManagerInterface $linkManager
     */
    public function __construct(
        DelegatingLinkTransformer $linkTransformer,
        ElementLinkManagerInterface $linkManager
    ) {
        $this->linkTransformer = $linkTransformer;
        $this->linkManager = $linkManager;
    }

    /**
     * @param ElementVersion         $elementVersion
     * @param string                 $language
     * @param TreeNodeInterface|null $node
     *
     * @return array
     */
    public function fetch(ElementVersion $elementVersion, $language, TreeNodeInterface $node = null)
    {
        /* @var $links ElementLink[] */
        $links = $this->linkManager->findBy(['elementVersion' => $elementVersion, 'language' => $language]);

        if ($node) {
            foreach ($this->linkManager->findBy(['type' => 'link-internal', 'language' => $language, 'target' => $node->getId()]) as $link) {
                $incomingLink = clone $link;
                $incomingLink->setType('link-incoming');
                $links[] = $incomingLink;
            }
        }

        $results = array();

        foreach ($links as $link) {
            $data = [
                'id' => $link->getId(),
                'iconCls' => 'p-element-component-icon',
                'language' => $link->getLanguage(),
                'type' => $link->getType(),
                'title' => $link->getField(),
                'content' => $link->getTarget(),
                'link' => [],
                'raw' => $link->getTarget(),
                'payload' => [],
            ];

            $results[] = $this->linkTransformer->transform($link, $data);
        }

        return $results;
    }
}
