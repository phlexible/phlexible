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
 * Link extractor interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface LinkExtractorInterface
{
    /**
     * @param ElementStructureValue $value
     * @param AbstractField         $field
     *
     * @return bool
     */
    public function supports(ElementStructureValue $value, AbstractField $field);

    /**
     * @param ElementStructureValue $value
     * @param AbstractField         $field
     *
     * @return ElementLink[]|null
     */
    public function extract(ElementStructureValue $value, AbstractField $field);
}
