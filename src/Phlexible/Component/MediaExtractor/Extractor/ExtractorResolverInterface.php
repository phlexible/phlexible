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
 * Extractor resolver interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ExtractorResolverInterface
{
    /**
     * Resolve extractor.
     *
     * @param InputDescriptor $input
     * @param MediaType       $mediaType
     * @param string          $targetFormat
     *
     * @return ExtractorInterface
     */
    public function resolve(InputDescriptor $input, MediaType $mediaType, $targetFormat);
}
