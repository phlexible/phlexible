<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaManagerBundle\MetaSet;

use Phlexible\Component\MediaType\Model\MediaType;

/**
 * Media type matcher.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaTypeMetasetMatcher
{
    /**
     * @var array
     */
    private $metasetMapping;

    /**
     * @param array $metasetMapping
     */
    public function __construct(array $metasetMapping)
    {
        $this->metasetMapping = $metasetMapping;
    }

    /**
     * @param MediaType $mediaType
     *
     * @return array
     */
    public function match(MediaType $mediaType)
    {
        $metasets = array();

        foreach ($this->metasetMapping as $metasetName => $mapping) {
            if ($this->matches($mediaType, $mapping)) {
                $metasets[] = $metasetName;
            }
        }

        return $metasets;
    }

    /**
     * @param MediaType $mediaType
     * @param array     $mapping
     *
     * @return bool
     */
    private function matches(MediaType $mediaType, array $mapping)
    {
        if (empty($mapping)) {
            return true;
        }

        $match = false;
        if (!empty($mapping['name'])) {
            $match = $mediaType->getName() === $mapping['name'];
        }
        if (!empty($mapping['category'])) {
            $match = $mediaType->getCategory() === $mapping['category'];
        }

        return $match;
    }
}
