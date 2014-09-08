<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ElementStructure\LinkExtractor;

use Phlexible\Bundle\ElementBundle\Entity\ElementLink;
use Phlexible\Bundle\ElementBundle\Model\ElementStructureValue;
use Phlexible\Bundle\ElementtypeBundle\Field\AbstractField;

/**
 * Text link extractor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TextLinkExtractor implements LinkExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(ElementStructureValue $value, AbstractField $field)
    {
        return $field->getDataType() === 'string';
    }

    /**
     * {@inheritdoc}
     */
    public function extract(ElementStructureValue $value, AbstractField $field)
    {
        if (!preg_match_all('/\[tid[:=](\d+)\]/', $value->getValue(), $matches)) {
            return array();
        }

        $links = array();

        foreach ($matches[1] as $treeId) {
            $link = new ElementLink();
            $link
                ->setType('link-internal')
                ->setLanguage($value->getLanguage())
                ->setField($value->getName())
                ->setTarget($treeId);

            $links[] = $link;
        }

        return $links;
    }
}