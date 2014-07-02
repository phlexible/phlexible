<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\AttributeReader;

use Phlexible\Bundle\MediaAssetBundle\MetaBag;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;

/**
 * Chain attribute reader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ChainAttributeReader implements AttributeReaderInterface
{
    /**
     * @var AttributeReaderInterface[]
     */
    private $readers;

    /**
     * @param AttributeReaderInterface[] $readers
     */
    public function __construct(array $readers)
    {
        $this->readers = $readers;
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
        foreach ($this->readers as $reader) {
            if ($reader->isAvailable() && $reader->supports($file)) {
                return true;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function read(FileInterface $file, MetaBag $metaBag)
    {
        foreach ($this->readers as $reader) {
            if ($reader->isAvailable() && $reader->supports($file)) {
                $reader->read($file, $metaBag);
            }
        }
    }
}