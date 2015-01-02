<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Meta;

use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFolderInterface;
use Phlexible\Component\MetaSet\Model\MetaSet;
use Phlexible\Component\MetaSet\Model\MetaSetManagerInterface;

/**
 * Folder meta set resolver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FolderMetaSetResolver
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
     * @param ExtendedFolderInterface $folder
     *
     * @return MetaSet[]
     */
    public function resolve(ExtendedFolderInterface $folder)
    {
        $metaSets = [];
        foreach ($folder->getMetasets() as $metaSetId) {
            $metaSet = $this->metaSetManager->find($metaSetId);
            if ($metaSet) {
                $metaSets[] = $metaSet;
            }
        }

        return $metaSets;
    }
}
