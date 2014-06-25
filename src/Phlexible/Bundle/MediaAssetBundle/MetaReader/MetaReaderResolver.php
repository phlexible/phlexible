<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\MetaReader;

use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;

/**
 * Meta reader resolver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaReaderResolver implements MetaReaderResolverInterface
{
    /**
     * @var MetaReaderInterface[]
     */
    private $readers;

    /**
     * @param MetaReaderInterface[] $readers
     */
    public function __construct(array $readers = array())
    {
        $this->readers = $readers;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(FileInterface $file)
    {
        foreach ($this->readers as $reader) {
            if ($reader->isAvailable() && $reader->supports($file)) {
                return $reader;
            }
        }

        return null;
    }
}
