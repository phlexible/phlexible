<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaExtractorBundle\ContentExtractor;

use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaType\Model\MediaType;

/**
 * Delegating content extractor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DelegatingContentExtractor implements ContentExtractorInterface
{
    /**
     * @var ContentExtractorResolverInterface
     */
    private $resolver;

    /**
     * @param ContentExtractorResolverInterface $resolver
     */
    public function __construct(ContentExtractorResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ExtendedFileInterface $file, MediaType $mediaType)
    {
        return null !== $this->resolver->resolve($file, $mediaType);
    }

    /**
     * {@inheritdoc}
     */
    public function extract(ExtendedFileInterface $file, MediaType $mediaType)
    {
        $extractor = $this->resolver->resolve($file, $mediaType);

        if (!$extractor) {
            return null;
        }

        return $extractor->extract($file, $mediaType);
    }
}
