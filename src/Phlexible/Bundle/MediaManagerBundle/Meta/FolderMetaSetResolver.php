<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Meta;

use Phlexible\Bundle\MediaSiteBundle\Model\FolderInterface;
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
     * @param FolderInterface $folder
     *
     * @return MetaSet[]
     */
    public function resolve(FolderInterface $folder)
    {
        $metaSets = array();
        $metaSetIds = $folder->getAttribute('metasets', array());
        foreach ($metaSetIds as $metaSetId) {
            $metaSets[] = $this->metaSetManager->find($metaSetId);
        }

        return $metaSets;
    }
}
