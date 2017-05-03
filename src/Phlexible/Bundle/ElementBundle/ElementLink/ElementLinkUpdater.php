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

use Phlexible\Bundle\ElementBundle\ElementLink\LinkExtractor\DelegatingLinkExtractor;
use Phlexible\Bundle\ElementBundle\Entity\ElementLink;
use Phlexible\Bundle\ElementBundle\Model\ElementLinkManagerInterface;
use Phlexible\Bundle\ElementBundle\Model\ElementStructure;
use Phlexible\Bundle\ElementtypeBundle\Field\FieldRegistry;

/**
 * Element link updater.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementLinkUpdater
{
    /**
     * @var DelegatingLinkExtractor
     */
    private $linkExtractor;

    /**
     * @var ElementLinkManagerInterface
     */
    private $linkManager;

    /**
     * @var FieldRegistry
     */
    private $fieldRegistry;

    /**
     * @param DelegatingLinkExtractor     $linkExtractor
     * @param ElementLinkManagerInterface $linkManager
     * @param FieldRegistry               $fieldRegistry
     */
    public function __construct(
        DelegatingLinkExtractor $linkExtractor,
        ElementLinkManagerInterface $linkManager,
        FieldRegistry $fieldRegistry
    ) {
        $this->linkExtractor = $linkExtractor;
        $this->linkManager = $linkManager;
        $this->fieldRegistry = $fieldRegistry;
    }

    /**
     * @param ElementStructure $elementStructure
     * @param bool             $flush
     */
    public function updateLinks(ElementStructure $elementStructure, $flush = true)
    {
        $links = $this->extractLinks($elementStructure);

        foreach ($links as $link) {
            $link->setElementVersion($elementStructure->getElementVersion());
        }

        $this->linkManager->updateElementLinks($links, $flush);
    }

    /**
     * @param ElementStructure $elementStructure
     *
     * @return ElementLink[]
     */
    private function extractLinks(ElementStructure $elementStructure)
    {
        $links = [];

        foreach ($elementStructure->getLanguages() as $language) {
            foreach ($elementStructure->getValues($language) as $elementStructureValue) {
                $field = $this->fieldRegistry->getField($elementStructureValue->getType());

                $links = array_merge($links, $this->linkExtractor->extract($elementStructureValue, $field));
            }
        }

        foreach ($elementStructure->getStructures() as $childStructure) {
            $links = array_merge($links, $this->extractLinks($childStructure));
        }

        return $links;
    }
}
