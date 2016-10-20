<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaManager\AttributeReader;

use Phlexible\Component\MediaType\Model\MediaType;
use Phlexible\Component\Volume\FileSource\PathSourceInterface;

/**
 * Zip extension attribute reader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ZipExtensionAttributeReader implements AttributeReaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
       return extension_loaded('zip');
    }

    /**
     * {@inheritdoc}
     */
    public function supports(PathSourceInterface $fileSource, MediaType $mediaType)
    {
        return $mediaType->getName() === 'zip';
    }

    /**
     * {@inheritdoc}
     */
    public function read(PathSourceInterface $fileSource, MediaType $mediaType, AttributeBag $attributes)
    {
        $filename = $fileSource->getPath();

        try {
            $zip = new \ZipArchive();
            $result = $zip->open($filename);

            if ($result === true) {
                if ($zip->comment) {
                    $attributes
                        ->set('zip.comment', $zip->comment);
                }

                if ($zip->numFiles) {
                    $attributes
                        ->set('zip.numFiles', $zip->numFiles);
                }

                if ($zip->status) {
                    $attributes
                        ->set('zip.status', $zip->status);
                }

                if ($zip->statusSys) {
                    $attributes
                        ->set('zip.statusSys', $zip->statusSys);
                }

                $zip->close();
            }
        } catch (\Exception $e) {
        }
    }
}
