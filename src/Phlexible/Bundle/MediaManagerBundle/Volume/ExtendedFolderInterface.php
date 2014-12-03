<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Volume;

use Phlexible\Component\Volume\Model\FolderInterface;

/**
 * Extended folder interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ExtendedFolderInterface extends FolderInterface
{
    /**
     * @param array $metasets
     *
     * @return $this
     */
    public function setMetasets(array $metasets);

    /**
     * @return array
     */
    public function getMetasets();

    /**
     * @param string $metaSetId
     *
     * @return $this
     */
    public function addMetaSet($metaSetId);

    /**
     * @param string $metaSetId
     *
     * @return $this
     */
    public function removeMetaSet($metaSetId);
}
