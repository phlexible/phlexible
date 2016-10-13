<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Phlexible\Bundle\MetaSetBundle\Entity\MetaDataValue;
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;

/**
 * File meta
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="media_file_meta")
 */
class FileMetaDataValue extends MetaDataValue
{
    /**
     * @var File
     * @ORM\ManyToOne(targetEntity="Phlexible\Bundle\MediaManagerBundle\Entity\File")
     * @ORM\JoinColumns(
     *   @ORM\JoinColumn(name="file_id", referencedColumnName="id"),
     *   @ORM\JoinColumn(name="file_version", referencedColumnName="version")
     * )
     */
    private $file;

    /**
     * @param string                $setId
     * @param ExtendedFileInterface $file
     * @param string                $language
     * @param string                $fieldId
     */
    public function __construct($setId, ExtendedFileInterface $file, $language, $fieldId)
    {
        parent::__construct($setId, $language, $fieldId);

        $this->file = $file;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param File $file
     *
     * @return $this
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }
}
