<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\ElementLink\LinkExtractor;

use Phlexible\Bundle\ElementBundle\Model\ElementStructureValue;
use Phlexible\Bundle\ElementtypeBundle\Field\AbstractField;

/**
 * Link extractor.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DelegatingLinkExtractor implements LinkExtractorInterface
{
    /**
     * @var LinkExtractorInterface[]
     */
    private $extractors = [];

    /**
     * @param LinkExtractorInterface[] $extractors
     */
    public function __construct(array $extractors = [])
    {
        foreach ($extractors as $extractor) {
            $this->addExtractor($extractor);
        }
    }

    /**
     * @param LinkExtractorInterface $extractor
     */
    private function addExtractor(LinkExtractorInterface $extractor)
    {
        $this->extractors[] = $extractor;
    }

    /**
     * @param ElementStructureValue $value
     * @param AbstractField         $field
     *
     * @return bool
     */
    public function supports(ElementStructureValue $value, AbstractField $field)
    {
        foreach ($this->extractors as $extractor) {
            if ($extractor->supports($value, $field)) {
                return true;
            }
        }

        return false;
    }

    public function extract(ElementStructureValue $value, AbstractField $field)
    {
        $links = [];
        foreach ($this->extractors as $extractor) {
            if ($extractor->supports($value, $field)) {
                foreach ($extractor->extract($value, $field) as $link) {
                    $links[] = $link;
                }
            }
        }

        return $links;
    }
}
