<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Volume\Model;

use Phlexible\Component\Volume\Exception\RuntimeException;
use Phlexible\Component\Volume\VolumeInterface;

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
     * @param FolderInterface|VolumeInterface $folder
     *
     * @throws RuntimeException
     */
    public function __construct($folder)
    {
        if (is_array($folder)) {
            $this->iterator = new \ArrayIterator($folder);
        } elseif ($folder instanceof FolderInterface) {
            $this->iterator = new \ArrayIterator([$folder]);
        } elseif ($folder instanceof VolumeInterface) {
            $this->iterator = new \ArrayIterator([$folder->findRootFolder()]);
        } else {
            throw new RuntimeException('FolderIterator needs either Volume or Folder.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->iterator->current();
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->current()->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->iterator->next();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->iterator->rewind();
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->iterator->valid();
    }

    /**
     * Get Iterator for currents element children.
     *
     * @return FolderIterator
     */
    public function getChildren()
    {
        return new FolderIterator($this->current()->getVolume()->findFoldersByParentFolder($this->current()));
    }

    /**
     * Check if current element has children.
     *
     * @return bool
     */
    public function hasChildren()
    {
        return $this->current()->getVolume()->countFoldersByParentFolder($this->current());
    }

}
