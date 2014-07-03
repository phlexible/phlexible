<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\MetaReader;

use Phlexible\Bundle\MediaAssetBundle\MetaBag;
use Phlexible\Bundle\MediaAssetBundle\MetaData;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;

/**
 * ID3 extension meta reader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Id3ExtensionMetaReader implements MetaReaderInterface
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
        return strtolower($file->getAttribute('assettype')) === 'audio';
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

        $tag = \id3_get_tag($filename, ID3_BEST);

        if (!empty($tag['title'])) {
            $metaData->set('title', $tag['title']);
        }

        if (!empty($tag['album'])) {
            $metaData->set('album', $tag['album']);
        }

        if (!empty($tag['artist'])) {
            $metaData->set('artist', $tag['artist']);
        }

        if (!empty($tag['year'])) {
            $metaData->set('year', $tag['year']);
        }

        if (!empty($tag['genre'])) {
            $metaData->set('genre', $tag['genre']);
        }

        if (!empty($tag['track'])) {
            $metaData->set('track', $tag['track']);
        }

        if (!empty($tag['comment'])) {
            $metaData->set('comment', $tag['comment']);
        }

        $metaBag->add($metaData);
    }

}