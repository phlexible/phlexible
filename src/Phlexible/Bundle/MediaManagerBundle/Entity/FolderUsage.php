<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Phlexible\Component\MediaManager\Volume\ExtendedFolderInterface;

/**
 * Folder usage.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="media_folder_usage")
 */
class FolderUsage
{
    const STATUS_ONLINE = 8;
    const STATUS_LATEST = 4;
    const STATUS_OLD = 2;
    const STATUS_DEAD = 1;

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
     * @var ExtendedFolderInterface
     * @ORM\ManyToOne(targetEntity="Phlexible\Bundle\MediaManagerBundle\Entity\Folder")
     * @ORM\JoinColumn(name="folder_id", referencedColumnName="id")
     */
    private $folder;

    /**
     * @param ExtendedFolderInterface $folder
     * @param string                  $usageType
     * @param string                  $usageId
     * @param int                     $status
     */
    public function __construct(ExtendedFolderInterface $folder, $usageType, $usageId, $status)
    {
        $this->folder = $folder;
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
     * @return ExtendedFolderInterface
     */
    public function getFolder()
    {
        return $this->folder;
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
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Return array represenattion of this usage.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'folderId' => $this->folder->getId(),
            'usageType' => $this->usageType,
            'usageId' => $this->usageId,
            'status' => $this->status,
        ];
    }
}
