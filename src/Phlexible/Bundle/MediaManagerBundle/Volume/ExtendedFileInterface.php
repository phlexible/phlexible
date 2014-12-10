<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Volume;

use Phlexible\Component\Volume\Model\FileInterface;

/**
 * File interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ExtendedFileInterface extends FileInterface
{
    /**
     * @param string $mediaCategory
     *
     * @return $this
     */
    public function setMediaCategory($mediaCategory);

    /**
     * @return string
     */
    public function getMediaCategory();

    /**
     * @param string $mediaType
     *
     * @return $this
     */
    public function setMediaType($mediaType);

    /**
     * @return string
     */
    public function getMediaType();

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
