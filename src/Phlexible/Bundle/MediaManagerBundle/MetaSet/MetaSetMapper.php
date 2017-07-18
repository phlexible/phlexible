<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaManagerBundle\MetaSet;

use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaType\Model\MediaType;
use Phlexible\Component\MetaSet\Model\MetaSetManagerInterface;

/**
 * Meta set mapper.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaSetMapper
{
    /**
     * @var MetaSetManagerInterface
     */
    private $metaSetManager;

    /**
     * @var MediaTypeMatcher
     */
    private $matcher;

    /**
     * @param MetaSetManagerInterface $metaSetManager
     * @param MediaTypeMatcher        $matcher
     */
    public function __construct(MetaSetManagerInterface $metaSetManager, MediaTypeMatcher $matcher)
    {
        $this->metaSetManager = $metaSetManager;
        $this->matcher = $matcher;
    }

    /**
     * @param ExtendedFileInterface $file
     * @param MediaType             $mediaType
     */
    public function map(ExtendedFileInterface $file, MediaType $mediaType)
    {
        foreach ($this->matcher->match($mediaType) as $metasetName) {
            $metaSet = $this->metaSetManager->findOneByName($metasetName);
            if ($metaSet) {
                $file->addMetaSet($metaSet->getId());
            }
        }
    }
}
