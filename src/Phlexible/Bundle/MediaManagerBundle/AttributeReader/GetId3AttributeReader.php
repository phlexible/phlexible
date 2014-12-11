<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\AttributeReader;

use GetId3\GetId3Core;
use Phlexible\Component\MediaType\Model\MediaType;
use Phlexible\Component\Volume\FileSource\PathSourceInterface;

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
    public function supports(PathSourceInterface $fileSource, MediaType $mediaType)
    {
        return in_array($mediaType->getCategory(), array('video', 'audio'));
    }

    /**
     * {@inheritdoc}
     */
    public function read(PathSourceInterface $fileSource, MediaType $mediaType, AttributeBag $attributes)
    {
        $filename = $fileSource->getPath();

        if (!id3_get_version($filename)) {
            return null;
        }

        $getId3 = new GetId3Core();
        $info = $getId3->analyze($filename);
        ldd($info);

        if (!empty($info['title'])) {
            $attributes->set('id3.title', $info['title']);
        }

        if (!empty($info['album'])) {
            $attributes->set('id3.album', $info['album']);
        }

        if (!empty($info['artist'])) {
            $attributes->set('id3.artist', $info['artist']);
        }

        if (!empty($info['year'])) {
            $attributes->set('id3.year', $info['year']);
        }

        if (!empty($info['genre'])) {
            $attributes->set('id3.genre', $info['genre']);
        }

        if (!empty($info['track'])) {
            $attributes->set('id3.track', $info['track']);
        }

        if (!empty($info['comment'])) {
            $attributes->set('id3.comment', $info['comment']);
        }
    }
}
