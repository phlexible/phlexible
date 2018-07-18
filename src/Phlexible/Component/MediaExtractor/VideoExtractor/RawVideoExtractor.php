<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaExtractor\VideoExtractor;

use Phlexible\Component\MediaCache\Worker\InputDescriptor;
use Phlexible\Component\MediaExtractor\Extractor\ExtractorInterface;
use Phlexible\Component\MediaType\Model\MediaType;

/**
 * Raw video extractor.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RawVideoExtractor implements ExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(InputDescriptor $input, MediaType $mediaType, $targetFormat)
    {
        return $targetFormat === 'video' && $mediaType->getCategory() === 'video';
    }

    /**
     * {@inheritdoc}
     */
    public function extract(InputDescriptor $input, MediaType $mediaType, $targetFormat)
    {
        return $input->getFilePath();
    }
}
