<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Phlexible\Bundle\MediaSiteBundle\Entity\File;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;

/**
 * File usage
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="media_file_usage")
 */
class FileUsage
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string", length=36, options={"fixed"=true})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="usage_type", type="string", length=255)
     */
    private $usageType;

    /**
     * @var string
     * @ORM\Column(name="usage_id", type="string", length=255)
     */
    private $usageId;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @var FileInterface
     * @ORM\ManyToOne(targetEntity="Phlexible\Bundle\MediaSiteBundle\Entity\File")
     * @ORM\JoinColumns(
     *   @ORM\JoinColumn(name="file_id", referencedColumnName="id"),
     *   @ORM\JoinColumn(name="file_version", referencedColumnName="version")
     * )
     */
    private $file;

    /**
     * @param FileInterface $file
     * @param string        $usageType
     * @param string        $usageId
     * @param int           $status
     */
    public function __construct(FileInterface $file, $usageType, $usageId, $status)
    {
        $this->file = $file;
        $this->usageType = $usageType;
        $this->usageId = $usageId;
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return FileInterface
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getUsageType()
    {
        return $this->usageType;
    }

    /**
     * @return string
     */
    public function getUsageId()
    {
        return $this->usageId;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Return array represenattion of this usage
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'fileId'      => $this->file->getId(),
            'fileVersion' => $this->file->getVersion(),
            'usageType'   => $this->usageType,
            'usageId'     => $this->usageId,
            'status'      => $this->status,
        );
    }
}