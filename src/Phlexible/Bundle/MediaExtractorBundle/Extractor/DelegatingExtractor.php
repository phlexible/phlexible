<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaExtractorBundle\Extractor;

use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;
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
