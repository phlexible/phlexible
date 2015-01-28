<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\AttributeReader;

use Phlexible\Component\MediaType\Model\MediaType;
use Phlexible\Component\Volume\FileSource\PathSourceInterface;
use PHPExiftool\Driver\Value\ValueInterface;
use PHPExiftool\Reader;

/**
 * GetId3 attribute reader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ExiftoolAttributeReader implements AttributeReaderInterface
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(PathSourceInterface $fileSource, MediaType $mediaType)
    {
        return in_array($mediaType->getCategory(), array('image', 'video', 'audio'));
    }

    /**
     * {@inheritdoc}
     */
    public function read(PathSourceInterface $fileSource, MediaType $mediaType, AttributeBag $attributes)
    {
        $filename = $fileSource->getPath();

        $metadatas = $this->reader->files($filename)->first();
        foreach ($metadatas as $metadata) {
            if (ValueInterface::TYPE_BINARY === $metadata->getValue()->getType()) {
                continue;
            }

            /* @var $metadata \PHPExiftool\Driver\Metadata\Metadata */
            $groupName = strtolower($metadata->getTag()->getGroupName());
            $name = strtolower($metadata->getTag()->getName());
            $value = (string) $metadata->getValue();
            if ($groupName === 'system') {
                continue;
            }

            $path = "exiftool.$groupName.$name = $value";
            $attributes->set($path, $value);
        }
    }
}
