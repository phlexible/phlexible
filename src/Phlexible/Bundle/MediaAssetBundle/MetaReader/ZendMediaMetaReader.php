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
use Psr\Log\LoggerInterface;

/**
 * Zend media meta reader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ZendMediaMetaReader implements MetaReaderInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
        return class_exists('Zend_Media');
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

        $metaData = new MetaData();
        $metaData->setTitle('Media');

        try
        {
            $id3 = new \Zend_Media_Id3v2($filename);

            if ($title = $id3->tit2->text) {
                $metaData->set('title', $title);
            }

            if ($artist = $id3->tpe1->text) {
                $metaData->set('artist', $artist);
            }

            if ($album = $id3->talb->text) {
                $metaData->set('album', $album);
            }

            if ($year = $id3->tyer->text) {
                $metaData->set('year', $year);
            }

            if ($comment = $id3->comm->text) {
                $metaData->set('comment', $comment);
            }

            if ($track = $id3->trck->text) {
                $metaData->set('track', $track);
            }

            if ($genre = $id3->tcon->text) {
                $metaData->set('genre', $genre);
            }

            $asset->getMetas()->add($metaData);
        } catch (\Exception $e) {
            $this->logger->error('ZendMediaMetaReader failed to read meta data from asset: ' . $e->getMessage());
        }
    }

}