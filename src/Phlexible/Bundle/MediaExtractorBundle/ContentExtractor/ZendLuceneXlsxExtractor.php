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
 * Zend lucene xlsx content extractor
 *
 * @author Phillip Look <plook@brainbits.net>
 */
class ZendLuceneXlsxExtractor implements ExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(ExtendedFileInterface $file, MediaType $mediaType, $targetFormat)
    {
        return $targetFormat === 'text' && $mediaType->getName() === 'xlsx';
    }

    /**
     * {@inheritdoc}
     */
    public function extract(ExtendedFileInterface $file, MediaType $mediaType)
    {
        $document = \Zend_Search_Lucene_Document_Xlsx::loadXlsxFile($file->getPhysicalPath());

        // set zend lucene document body as content
        $content = new Content($document->getFieldUtf8Value('body'));

        return $content;
    }

}
