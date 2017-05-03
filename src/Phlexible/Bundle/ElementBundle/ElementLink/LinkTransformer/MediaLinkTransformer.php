<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\ElementLink\LinkTransformer;

use Phlexible\Bundle\ElementBundle\Entity\ElementLink;
use Phlexible\Component\Volume\VolumeManager;

/**
 * Media link transformer.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaLinkTransformer implements LinkTransformerInterface
{
    /**
     * @var VolumeManager
     */
    private $volumeManager;

    /**
     * @param VolumeManager $volumeManager
     */
    public function __construct(VolumeManager $volumeManager)
    {
        $this->volumeManager = $volumeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ElementLink $elementLink)
    {
        return in_array($elementLink->getType(), ['file', 'folder']);
    }

    /**
     * {@inheritdoc}
     */
    public function transform(ElementLink $elementLink, array $data)
    {
        if ($elementLink->getType() === 'file') {
            list($fileId, $fileVersion) = explode(';', $elementLink->getTarget());
            $volume = $this->volumeManager->findByFileId($fileId);

            if ($volume) {
                $file = $volume->findFile($fileId, $fileVersion);
                if ($file) {
                    $data['content'] = $file->getName();
                    $data['payload']['name'] = $file->getName();

                    $folder = $volume->findFolder($file->getFolderId());
                    $folderPath = [$folder->getId()];
                    while ($folder = $volume->findFolder($folder->getParentId())) {
                        array_unshift($folderPath, $folder->getId());
                    }
                    $data['payload']['folderPath'] = $folderPath;
                }
            }

            $data['payload']['fileId'] = $fileId;
            $data['payload']['fileVersion'] = $fileVersion;
            $data['iconCls'] = 'p-mediamanager-image-icon';
        } elseif ($elementLink->getType() === 'folder') {
            $folderId = $elementLink->getTarget();
            $volume = $this->volumeManager->findByFolderId($folderId);
            if ($volume) {
                $folder = $volume->findFolder($folderId);
                if ($folder) {
                    $data['content'] = $folder->getName();
                    $data['payload']['name'] = $folder->getName();

                    $folderPath = [$folder->getId()];
                    while ($folder = $volume->findFolder($folder->getParentId())) {
                        array_unshift($folderPath, $folder->getId());
                    }
                    $data['payload']['folderPath'] = $folderPath;
                }
            }

            $data['payload']['folderId'] = $folderId;
            $data['iconCls'] = 'p-mediamanager-folder-icon';
        }

        return $data;
    }
}
