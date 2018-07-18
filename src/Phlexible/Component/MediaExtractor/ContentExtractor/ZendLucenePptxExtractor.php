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
 * Zend lucene pptx content extractor.
 *
 * @author Phillip Look <plook@brainbits.net>
 */
class ZendLucenePptxExtractor implements ExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(InputDescriptor $input, MediaType $mediaType, $targetFormat)
    {
        return $targetFormat === 'text' && $mediaType->getName() === 'pptx';
    }

    /**
     * {@inheritdoc}
     */
    public function extract(InputDescriptor $input, MediaType $mediaType, $targetFormat)
    {
        $document = \Zend_Search_Lucene_Document_Pptx::loadPptxFile($input->getFilePath());

        // set zend lucene document body as content
        $content = new Content($document->getFieldUtf8Value('body'));

        return $content;
    }
}
