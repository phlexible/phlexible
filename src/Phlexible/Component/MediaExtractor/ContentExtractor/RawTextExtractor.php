<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaExtractor\ContentExtractor;

use Phlexible\Component\MediaCache\Worker\InputDescriptor;
use Phlexible\Component\MediaExtractor\Extractor\ExtractorInterface;
use Phlexible\Component\MediaType\Model\MediaType;

/**
 * Raw text content extract.
 *
 * @author Phillip Look <plook@brainbits.net>
 */
class RawTextExtractor implements ExtractorInterface
{
    /**
     * @var string
     */
    private $encoding;

    /**
     * @param string $encoding
     */
    public function __construct($encoding = 'UTF-8')
    {
        $this->encoding = $encoding;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(InputDescriptor $input, MediaType $mediaType, $targetFormat)
    {
        return $targetFormat === 'text' && $mediaType->getCategory() === 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function extract(InputDescriptor $input, MediaType $mediaType, $targetFormat)
    {
        // fetch text from file
        $contents = file_get_contents($input->getFilePath());

        // ensure utf8 encoding
        $fromEncoding = mb_detect_encoding($contents);
        mb_convert_encoding($contents, $fromEncoding, $this->encoding);

        // use text as content
        $content = new Content($contents);

        return $content;
    }
}
