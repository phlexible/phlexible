<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\ContentExtractor;

use Phlexible\Bundle\MediaAssetBundle\Content;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;

/**
 * Zend lucene xlsx content extractor
 *
 * @author Phillip Look <plook@brainbits.net>
 */
class ZendLuceneXlsxExtractor implements ContentExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
        return class_exists('Zend_Search_Lucene_Document_Xlsx');
    }

    /**
     * {@inheritdoc}
     */
    public function supports(FileInterface $file)
    {
        return strtolower($file->getAttribute('documenttype')) === 'xlsx';
    }

    /**
     * {@inheritdoc}
     */
    public function extract(FileInterface $file)
    {
        $document = \Zend_Search_Lucene_Document_Xlsx::loadXlsxFile($file->getPhysicalPath());

        // set zend lucene document body as content
        $content = new Content($document->getFieldUtf8Value('body'));

        return $content;
    }

}