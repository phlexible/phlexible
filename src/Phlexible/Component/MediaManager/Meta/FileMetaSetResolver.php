<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaManager\Meta;

use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MetaSet\Model\MetaSet;
use Phlexible\Component\MetaSet\Model\MetaSetManagerInterface;

/**
 * File meta set resolver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FileMetaSetResolver
{
    /**
     * @var MetaSetManagerInterface
     */
    private $metaSetManager;

    /**
     * @param MetaSetManagerInterface $metaSetManager
     */
    public function __construct(MetaSetManagerInterface $metaSetManager)
    {
        $this->metaSetManager = $metaSetManager;
    }

    /**
     * @param ExtendedFileInterface $file
     *
     * @return MetaSet[]
     */
    public function resolve(ExtendedFileInterface $file)
    {
        $metaSets = [];
        foreach ($file->getMetasets() as $metaSetId) {
            $metaSet = $this->metaSetManager->find($metaSetId);
            if ($metaSet) {
                $metaSets[] = $metaSet;
            }
        }

        return $metaSets;
    }
}
