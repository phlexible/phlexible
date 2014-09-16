<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaExtractorBundle\ImageExtractor;

use FFMpeg\FFMpeg;
use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;

/**
 * Video converter extractor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class VideoConverterImageExtractor implements ImageExtractorInterface
{
    /**
     * @var FFMpeg
     */
    private $converter;

    /**
     * @var string
     */
    private $tempDir;

    /**
     * @param FFMpeg $converter
     * @param string $tempDir
     */
    public function __construct(FFMpeg $converter, $tempDir)
    {
        $this->converter = $converter;
        $this->tempDir = $tempDir;
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
        return true;//$this->converter->isAvailable();
    }

    /**
     * {@inheritdoc}
     */
    public function supports(FileInterface $file)
    {
        return strtolower($file->getAssettype()) === 'video';
    }

    /**
     * {@inheritdoc}
     */
    public function extract(FileInterface $file)
    {
        $filename = $file->getPhysicalPath();

        $imageFilename = null;

        try {
            $imageFilename = $this->tempDir . '/' . $file->getId() . '.jpg';

            $video = $this->converter->open($filename);
            $video->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds(5))
                ->save($imageFilename);
        } catch (\Exception $e) {
            $imageFile = null;
        }

        return $imageFilename;
    }
}
