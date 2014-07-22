<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaExtractorBundle\ContentExtractor;

use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;

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
    public function __construct(array $extractors = array())
    {
        $this->extractors = $extractors;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(FileInterface $file)
    {
        foreach ($this->extractors as $reader) {
            if ($reader->isAvailable() && $reader->supports($file)) {
                return $reader;
            }
        }

        return null;
    }
}
