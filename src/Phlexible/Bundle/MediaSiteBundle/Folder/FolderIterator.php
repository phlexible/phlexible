<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Folder;

use Phlexible\Bundle\MediaSiteBundle\Site\SiteInterface;

/**
 * Folder iterator
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class FolderIterator implements \Iterator, \RecursiveIterator
{
    /**
     * @var \Iterator
     */
    private $iterator;

    /**
     * @param FolderInterface|SiteInterface $folder
     *
     * @throws \RuntimeException
     */
    public function __construct($folder)
    {
        if ($folder instanceof FolderInterface) {
            $this->iterator = new \ArrayIterator($folder->getSite()->findFoldersByParentFolder($folder));
        } elseif ($folder instanceof SiteInterface) {
            $this->iterator = new \ArrayIterator(array($folder->findRootFolder()));
        } else {
            throw new \RuntimeException('FolderIterator needs either Site or Folder.');
        }
    }

    /**
     * @return Folder
     */
    public function current()
    {
        return $this->iterator->current();
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->current()->getId();
    }

    public function next()
    {
        $this->iterator->next();
    }

    public function rewind()
    {
        $this->iterator->rewind();
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->iterator->valid();
    }

    /**
     * Get Iterator for currents element children.
     *
     * @return Folder
     */
    public function getChildren()
    {
        return new FolderIterator($this->current());
    }

    /**
     * Check if current element has children.
     *
     * @return bool
     */
    public function hasChildren()
    {
        return $this->current()->getSite()->countFoldersByParentFolder($this->current());
    }

}
