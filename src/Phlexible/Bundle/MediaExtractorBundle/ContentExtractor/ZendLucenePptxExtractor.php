<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaExtractorBundle\ContentExtractor;

use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;

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
    public function isAvailable()
    {
        return class_exists('Zend_Search_Lucene_Document_Pptx');
    }

    /**
     * {@inheritdoc}
     */
    public function supports(FileInterface $file)
    {
        return strtolower($file->getAttribute('documenttype')) === 'pptx';
    }

    /**
     * {@inheritdoc}
     */
    public function extract(FileInterface $file)
    {
        $document = \Zend_Search_Lucene_Document_Pptx::loadPptxFile($file->getPhysicalPath());

        // set zend lucene document body as content
        $content = new Content($document->getFieldUtf8Value('body'));

        return $content;
    }

}