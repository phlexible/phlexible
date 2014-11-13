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
 * Raw text content extract
 *
 * @author Phillip Look <plook@brainbits.net>
 */
class RawTextExtractor implements ContentExtractorInterface
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
    public function isAvailable()
    {
        return extension_loaded('mb_string') && function_exists('file_get_contents');
    }

    /**
     * {@inheritdoc}
     */
    public function supports(FileInterface $file)
    {
        return strtolower($file->getAssettype()) === 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function extract(FileInterface $file)
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
