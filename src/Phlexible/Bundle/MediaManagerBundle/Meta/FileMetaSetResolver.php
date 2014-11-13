<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Meta;

use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;
use Phlexible\Bundle\MetaSetBundle\Entity\MetaSet;
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
     * @param FileInterface $file
     *
     * @return MetaSet[]
     */
    public function resolve(FileInterface $file)
    {
        $metaSets = [];
        $metaSetIds = $file->getAttribute('metasets', []);
        foreach ($metaSetIds as $metaSetId) {
            $metaSets[] = $this->metaSetManager->find($metaSetId);
        }

        return $metaSets;
    }
}
