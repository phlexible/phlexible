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
