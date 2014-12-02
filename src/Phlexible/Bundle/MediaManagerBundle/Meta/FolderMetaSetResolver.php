<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Meta;

use Phlexible\Bundle\MediaManagerBundle\Site\ExtendedFolderInterface;
use Phlexible\Bundle\MetaSetBundle\Entity\MetaSet;
use Phlexible\Bundle\MetaSetBundle\Model\MetaSetManagerInterface;

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
            $metaSets[] = $this->metaSetManager->find($metaSetId);
        }

        return $metaSets;
    }
}
