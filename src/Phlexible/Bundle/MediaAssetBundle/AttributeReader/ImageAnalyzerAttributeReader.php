<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\AttributeReader;

use Brainbits\ImageAnalyzer\ImageAnalyzer;
use Phlexible\Bundle\MediaAssetBundle\AttributeMetaData;
use Phlexible\Bundle\MediaAssetBundle\Attributes;
use Phlexible\Bundle\MediaAssetBundle\AttributesBag;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;

/**
 * Imagemagick based attribute reader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ImageAnalyzerAttributeReader implements AttributeReaderInterface
{
    /**
     * @var ImageAnalyzer
     */
    private $analyzer;

    /**
     * @param ImageAnalyzer $analyzer
     */
    public function __construct(ImageAnalyzer $analyzer)
    {
        $this->analyzer = $analyzer;
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
    public function supports(FileInterface $file)
    {
        return strtolower($file->getAttribute('assettype')) === 'image';
    }

    /**
     * {@inheritdoc}
     */
    public function read(FileInterface $file, AttributesBag $attributes)
    {
        $filename = $file->getPhysicalPath();

        try {
            $result = $this->analyzer->analyze($filename);

            $attributes
                ->set('image.width', $result->getWidth())
                ->set('image.height', $result->getHeight())
                ->set('image.format', $result->getFormat())
                ->set('image.colors', $result->getColors())
                ->set('image.colorspace', strtoupper($result->getColorspace()))
                ->set('image.depth', $result->getDepth())
                ->set('image.quality', $result->getQuality())
                ->set('image.resolution', $result->getWidth() . 'x' . $result->getHeight())
                ->set('image.profiles', implode(',', $result->getProfiles()));
        } catch (\Exception $e) {
        }
    }
}
