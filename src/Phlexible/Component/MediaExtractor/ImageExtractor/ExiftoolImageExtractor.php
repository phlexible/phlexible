<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaExtractor\ImageExtractor;

use Phlexible\Component\MediaCache\Worker\InputDescriptor;
use Phlexible\Component\MediaExtractor\Extractor\ExtractorInterface;
use Phlexible\Component\MediaType\Model\MediaType;
use PHPExiftool\Driver\Metadata\Metadata;
use PHPExiftool\Driver\Value\ValueInterface;
use PHPExiftool\Reader;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Exiftool image extractor.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ExiftoolImageExtractor implements ExtractorInterface
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var string
     */
    private $tempDir;

    /**
     * @param Reader $reader
     * @param string $tempDir
     */
    public function __construct(Reader $reader, $tempDir)
    {
        $this->reader = $reader;
        $this->tempDir = $tempDir;
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
    public function supports(InputDescriptor $input, MediaType $mediaType, $targetFormat)
    {
        return $targetFormat === 'image' && $mediaType->getCategory() === 'audio';
    }

    /**
     * {@inheritdoc}
     */
    public function extract(InputDescriptor $input, MediaType $mediaType, $targetFormat)
    {
        $filename = $input->getFilePath();

        if (!file_exists($filename)) {
            return null;
        }

        $metadatas = $this->reader->reset()->files($filename)->first();

        $imageFilename = $this->tempDir.'/'.uniqid('exiftool-', true).'.jpg';

        foreach ($metadatas as $metadata) {
            /* @var $metadata Metadata */

            if ($metadata->getTag()->getName() !== 'Picture' && ValueInterface::TYPE_BINARY !== $metadata->getValue()->getType()) {
                continue;
            }

            $content = (string) $metadata->getValue()->asString();
            $filesystem = new Filesystem();
            $filesystem->dumpFile($imageFilename, $content);

            return $imageFilename;
        }

        return null;
    }
}
