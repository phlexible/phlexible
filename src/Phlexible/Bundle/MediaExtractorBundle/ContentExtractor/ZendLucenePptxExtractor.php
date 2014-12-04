<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaExtractorBundle\ContentExtractor;

use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaType\Model\MediaType;

/**
 * Zend lucene pptx content extractor
 *
 * @author Phillip Look <plook@brainbits.net>
 */
class ZendLucenePptxExtractor implements ContentExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(ExtendedFileInterface $file, MediaType $mediaType)
    {
        return $mediaType->getName() === 'pptx';
    }

    /**
     * {@inheritdoc}
     */
    public function extract(ExtendedFileInterface $file, MediaType $mediaType)
    {
        $document = \Zend_Search_Lucene_Document_Pptx::loadPptxFile($file->getPhysicalPath());

        // set zend lucene document body as content
        $content = new Content($document->getFieldUtf8Value('body'));

        return $content;
    }

}
