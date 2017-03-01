<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\ElementStructure\LinkExtractor;

use Phlexible\Bundle\ElementBundle\Entity\ElementLink;
use Phlexible\Bundle\ElementBundle\Model\ElementStructureValue;
use Phlexible\Bundle\ElementtypeBundle\Field\AbstractField;

/**
 * Media link extractor.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaLinkExtractor implements LinkExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(ElementStructureValue $value, AbstractField $field)
    {
        return in_array($value->getType(), ['file', 'folder']);
    }

    /**
     * {@inheritdoc}
     */
    public function extract(ElementStructureValue $value, AbstractField $field)
    {
        if (!$value->getValue()) {
            return [];
        }

        $link = new ElementLink();
        $link
            ->setType($value->getType())
            ->setLanguage($value->getLanguage())
            ->setField($value->getName())
            ->setTarget($value->getValue());

        return [$link];
    }
}
