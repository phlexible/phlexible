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
use Phlexible\Bundle\MediaAssetBundle\MetaBag;
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
    public function read(FileInterface $file, MetaBag $metaBag)
    {
        $filename = $file->getPhysicalPath();

        $metaData = new AttributeMetaData();
        $metaData->setTitle('Image attributes');

        try {
            $result = $this->analyzer->analyze($filename);

            $metaData
                ->set('width', $result->getWidth())
                ->set('height', $result->getHeight())
                ->set('x_ratio', $metaData->get('width') / $metaData->get('height'))
                ->set('y_ratio', $metaData->get('height') / $metaData->get('width'))
                ->set('format', $result->getFormat())
                ->set('colors', $result->getColors())
                ->set('colorspace', strtoupper($result->getColorspace()))
                ->set('depth', $result->getDepth())
                ->set('quality', $result->getQuality())
                ->set('resolution', $result->getWidth() . 'x' . $result->getHeight())
                ->set('profiles', implode(',', $result->getProfiles()))
            ;

            $metaBag->add($metaData);
        } catch (\Exception $e) {
        }
    }
}
