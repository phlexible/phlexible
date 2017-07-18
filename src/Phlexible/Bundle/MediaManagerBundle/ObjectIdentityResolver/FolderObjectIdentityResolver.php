<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaManagerBundle\ObjectIdentityResolver;

use Phlexible\Bundle\MediaManagerBundle\Entity\Folder;
use Phlexible\Component\AccessControl\Domain\HierarchicalObjectIdentity;
use Phlexible\Component\AccessControl\ObjectIdentityResolver\ObjectIdentityResolverInterface;
use Phlexible\Component\Volume\VolumeManager;

/**
 * Folder object identity resolver.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FolderObjectIdentityResolver implements ObjectIdentityResolverInterface
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
    public function resolve($objectType, $objectId)
    {
        if ($objectType !== Folder::class) {
            return null;
        }

        $volume = $this->volumeManager->getByFolderId($objectId);
        $folder = $volume->findFolder($objectId);

        return $objectIdentity = HierarchicalObjectIdentity::fromDomainObject($folder);
    }
}
