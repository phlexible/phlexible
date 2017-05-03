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
use Phlexible\Bundle\MetaSetBundle\Entity\MetaDataValue;
use Phlexible\Component\MediaManager\Volume\ExtendedFolderInterface;

/**
 * Folder meta.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="media_folder_meta")
 */
class FolderMetaDataValue extends MetaDataValue
{
    /**
     * @var Folder
     * @ORM\ManyToOne(targetEntity="Phlexible\Bundle\MediaManagerBundle\Entity\Folder")
     * @ORM\JoinColumn(name="folder_id", referencedColumnName="id", onDelete="cascade")
     */
    private $folder;

    /**
     * @param string                  $setId
     * @param ExtendedFolderInterface $folder
     * @param string                  $language
     * @param string                  $fieldId
     */
    public function __construct($setId, ExtendedFolderInterface $folder, $language, $fieldId)
    {
        parent::__construct($setId, $language, $fieldId);

        $this->folder = $folder;
    }

    /**
     * @return Folder
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * @param Folder $folder
     *
     * @return $this
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;

        return $this;
    }
}
