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
use Phlexible\Component\Elementtype\Field\AbstractField;

/**
 * Text link extractor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LinkFieldLinkExtractor implements LinkExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(ElementStructureValue $value, AbstractField $field)
    {
        return $value->getType() === 'link';
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
            ->setLanguage($value->getLanguage())
            ->setField($value->getName());

        $rawValue = $value->getValue();
        $type = $rawValue['type'];
        if (in_array($type, ['internal', 'intrasiteroot']) && !empty($rawValue['tid'])) {
            $link
                ->setType('link-internal')
                ->setTarget($rawValue['tid']);
        } elseif ($type === 'external' && !empty($rawValue['url'])) {
            $link
                ->setType('link-external')
                ->setTarget($rawValue['url']);
        } elseif ($type === 'mailto' && !empty($rawValue['recipient'])) {
            $link
                ->setType('link-mailto')
                ->setTarget($rawValue['recipient']);
        } else {
            return [];
        }

        return [$link];
    }
}
