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

use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaType\Model\MediaType;

/**
 * Delegating extractor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DelegatingExtractor implements ExtractorInterface
{
    /**
     * @var ExtractorResolverInterface
     */
    private $resolver;

    /**
     * @param ExtractorResolverInterface $resolver
     */
    public function __construct(ExtractorResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ExtendedFileInterface $file, MediaType $mediaType, $targetFormat)
    {
        return null !== $this->resolver->resolve($file, $mediaType, $targetFormat);
    }

    /**
     * {@inheritdoc}
     */
    public function extract(ExtendedFileInterface $file, MediaType $mediaType, $targetFormat)
    {
        $extractor = $this->resolver->resolve($file, $mediaType, $targetFormat);

        if (!$extractor) {
            return null;
        }

        return $extractor->extract($file, $mediaType, $targetFormat);
    }
}
