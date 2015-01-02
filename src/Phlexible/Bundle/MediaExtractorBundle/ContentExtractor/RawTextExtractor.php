<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaExtractorBundle\ContentExtractor;

use Phlexible\Bundle\MediaExtractorBundle\Extractor\ExtractorInterface;
use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaType\Model\MediaType;

/**
 * Raw text content extract
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
    public function supports(ExtendedFileInterface $file, MediaType $mediaType, $targetFormat)
    {
        return $targetFormat === 'text' && $mediaType->getCategory() === 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function extract(ExtendedFileInterface $file, MediaType $mediaType, $targetFormat)
    {
        // fetch text from file
        $contents = file_get_contents($file->getPhysicalPath());

        // ensure utf8 encoding
        $fromEncoding = mb_detect_encoding($contents);
        mb_convert_encoding($contents, $fromEncoding, $this->encoding);

        // use text as content
        $content = new Content($contents);

        return $content;
    }

}
