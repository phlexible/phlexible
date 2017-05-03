<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\ElementLink\LinkTransformer;

use Phlexible\Bundle\ElementBundle\Entity\ElementLink;

/**
 * Link transformer interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface LinkTransformerInterface
{
    /**
     * @param ElementLink $elementLink
     *
     * @return bool
     */
    public function supports(ElementLink $elementLink);

    /**
     * @param ElementLink $elementLink
     *
     * @return array
     */
    public function transform(ElementLink $elementLink, array $data);
}
