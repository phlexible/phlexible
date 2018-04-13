<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaExtractor\Extractor;

use Phlexible\Component\MediaCache\Worker\InputDescriptor;
use Phlexible\Component\MediaType\Model\MediaType;

/**
 * Extractor resolver.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ExtractorResolver implements ExtractorResolverInterface
{
    /**
     * @var ExtractorInterface[]
     */
    private $extractors;

    /**
     * @param ExtractorInterface[] $extractors
     */
    public function __construct(array $extractors = [])
    {
        $this->extractors = $extractors;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(InputDescriptor $input, MediaType $mediaType, $targetFormat)
    {
        foreach ($this->extractors as $extractor) {
            if ($extractor->supports($input, $mediaType, $targetFormat)) {
                return $extractor;
            }
        }

        return null;
    }
}
