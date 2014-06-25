<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\MetaReader;

use Phlexible\Bundle\MediaAssetBundle\MetaBag;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;

/**
 * Delegating meta reader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DelegatingMetaReader implements MetaReaderInterface
{
    /**
     * @var MetaReaderResolverInterface
     */
    private $resolver;

    /**
     * @param MetaReaderResolverInterface $resolver
     */
    public function __construct(MetaReaderResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @return boolean
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
    public function read(FileInterface $file, MetaBag $metaBag)
    {
        $reader = $this->resolver->resolve($file);

        if (!$reader) {
            return;
        }

        $reader->read($file, $metaBag);
    }
}