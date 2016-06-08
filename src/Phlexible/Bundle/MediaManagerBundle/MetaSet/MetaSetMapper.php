<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\MetaSet;

use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaType\Model\MediaType;
use Phlexible\Component\MetaSet\Model\MetaSetManagerInterface;

/**
 * Meta set mapper
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
     * @var array
     */
    private $metasetMapping;

    /**
     * @param MetaSetManagerInterface $metaSetManager
     * @param array                   $metasetMapping
     */
    public function __construct(MetaSetManagerInterface $metaSetManager, array $metasetMapping)
    {
        $this->metaSetManager = $metaSetManager;
        $this->metasetMapping = $metasetMapping;
    }

    /**
     * @param ExtendedFileInterface $file
     * @param MediaType             $mediaType
     */
    public function map(ExtendedFileInterface $file, MediaType $mediaType)
    {
        foreach ($this->metasetMapping as $metasetName => $mapping) {
            if ($this->matches($mediaType, $mapping)) {
                $metaSet = $this->metaSetManager->findOneByName($metasetName);
                if ($metaSet) {
                    $file->addMetaSet($metaSet->getId());
                }
            }
        }
    }

    /**
     * @param MediaType $mediaType
     * @param array     $mapping
     *
     * @return bool
     */
    private function matches(MediaType $mediaType, array $mapping)
    {
        if (empty($mapping)) {
            return true;
        }

        $match = false;
        if (!empty($mapping['name'])) {
            $match = $mediaType->getName() === $mapping['name'];
        }
        if (!empty($mapping['category'])) {
            $match = $mediaType->getCategory() === $mapping['category'];
        }

        return $match;
    }
}
