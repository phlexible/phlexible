<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\AttributeReader;

use GetId3\GetId3Core;
use Phlexible\Bundle\MediaAssetBundle\MetaBag;
use Phlexible\Bundle\MediaAssetBundle\MetaData;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;

/**
 * GetId3 attribute reader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GetId3AttributeReader implements AttributeReaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
        return extension_loaded('id3') && function_exists('id3_get_tag');
    }

    /**
     * {@inheritdoc}
     */
    public function supports(FileInterface $file)
    {
        return strtolower($file->getAttribute('assettype')) === 'video' || strtolower($file->getAttribute('assettype')) === 'audio';
    }

    /**
     * {@inheritdoc}
     */
    public function read(FileInterface $file, MetaBag $metaBag)
    {
        $filename = $file->getPhysicalPath();

        if (!id3_get_version($filename)) {
            return null;
        }

        $metaData = new MetaData();
        $metaData->setTitle('ID3');

        $getId3 = new GetId3Core();
        $info = $getId3->analyze($filename);
        ldd($info);

        if (!empty($info['title'])) {
            $metaData->set('title', $info['title']);
        }

        if (!empty($info['album'])) {
            $metaData->set('album', $info['album']);
        }

        if (!empty($info['artist'])) {
            $metaData->set('artist', $info['artist']);
        }

        if (!empty($info['year'])) {
            $metaData->set('year', $info['year']);
        }

        if (!empty($info['genre'])) {
            $metaData->set('genre', $info['genre']);
        }

        if (!empty($info['track'])) {
            $metaData->set('track', $info['track']);
        }

        if (!empty($info['comment'])) {
            $metaData->set('comment', $info['comment']);
        }

        $metaBag->add($metaData);
    }

}