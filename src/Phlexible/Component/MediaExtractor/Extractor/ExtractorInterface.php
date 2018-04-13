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
 * Extractor interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ExtractorInterface
{
    /**
     * Check if extractor supports the given asset.
     *
     * @param InputDescriptor $input
     * @param MediaType       $mediaType
     * @param string          $targetFormat
     *
     * @return bool
     */
    public function supports(InputDescriptor $input, MediaType $mediaType, $targetFormat);

    /**
     * Extract from file.
     *
     * @param InputDescriptor $input
     * @param MediaType       $mediaType
     * @param string          $targetFormat
     *
     * @return string
     */
    public function extract(InputDescriptor $input, MediaType $mediaType, $targetFormat);
}
