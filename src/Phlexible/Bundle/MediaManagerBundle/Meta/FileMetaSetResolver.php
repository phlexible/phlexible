<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Meta;

use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;
use Phlexible\Bundle\MetaSetBundle\Model\MetaSet;
use Phlexible\Bundle\MetaSetBundle\Model\MetaSetManagerInterface;

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
