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
 * Content extractor resolver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ContentExtractorResolver implements ContentExtractorResolverInterface
{
    /**
     * @var ContentExtractorInterface[]
     */
    private $extractors;

    /**
     * @param ContentExtractorInterface[] $extractors
     */
    public function __construct(array $extractors = [])
    {
        $this->extractors = $extractors;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(ExtendedFileInterface $file, MediaType $mediaType)
    {
        foreach ($this->extractors as $reader) {
            if ($reader->supports($file, $mediaType)) {
                return $reader;
            }
        }

        return null;
    }
}
