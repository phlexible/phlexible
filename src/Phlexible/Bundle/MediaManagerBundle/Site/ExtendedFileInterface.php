<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Site;

use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;

/**
 * File interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ExtendedFileInterface extends FileInterface
{
    /**
     * @param string $documenttype
     *
     * @return $this
     */
    public function setDocumenttype($documenttype);

    /**
     * @return string
     */
    public function getDocumenttype();

    /**
     * @param string $assetType
     *
     * @return $this
     */
    public function setAssetType($assetType);

    /**
     * @return string
     */
    public function getAssetType();

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
