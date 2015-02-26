<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Phlexible\Component\MediaManager\Volume\ExtendedFolderInterface;
use Phlexible\Component\Volume\Model\Folder as BaseFolder;

/**
 * Folder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="media_folder")
 */
class Folder extends BaseFolder implements ExtendedFolderInterface
{
    /**
     * @var array
     * @ORM\Column(name="metasets", type="simple_array", nullable=true)
     */
    private $metasets = [];

    /**
     * @param string $metaSetId
     *
     * @return $this
     */
    public function addMetaSet($metaSetId)
    {
        if ($metaSetId && !in_array($metaSetId, $this->metasets)) {
            $this->metasets[] = $metaSetId;
        }

        return $this;
    }

    /**
     * @param string $metaSetId
     *
     * @return $this
     */
    public function removeMetaSet($metaSetId)
    {
        if (in_array($metaSetId, $this->metasets)) {
            unset($this->metasets[array_search($metaSetId, $this->metasets)]);
        }

        return $this;
    }

    /**
     * @param array $metasets
     *
     * @return $this
     */
    public function setMetaSets(array $metasets)
    {
        $this->metasets = $metasets;

        return $this;
    }

    /**
     * @return array
     */
    public function getMetaSets()
    {
        return $this->metasets;
    }
}
