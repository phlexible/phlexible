<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\ContentExtractor;

use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;

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
     * @return bool
     */
    public function isAvailable()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(FileInterface $file)
    {
        return null === $this->resolver->resolve($file) ? false : true;
    }

    /**
     * {@inheritdoc}
     */
    public function extract(FileInterface $file)
    {
        $extractor = $this->resolver->resolve($file);

        if (!$extractor) {
            return null;
        }

        return $extractor->extract($file);
    }
}