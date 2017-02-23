<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaCache\Storage;

use Phlexible\Component\MediaCache\Exception\InvalidArgumentException;

/**
 * Storage manager.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class StorageManager
{
    /**
     * @var StorageInterface[]
     */
    private $storages = [];

    /**
     * @param StorageInterface[] $storages
     */
    public function __construct(array $storages = [])
    {
        foreach ($storages as $name => $storage) {
            $this->add($name, $storage);
        }
    }

    /**
     * @param string           $name
     * @param StorageInterface $storage
     *
     * @return $this
     */
    public function add($name, StorageInterface $storage)
    {
        $this->storages[$name] = $storage;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return StorageInterface
     *
     * @throws InvalidArgumentException
     */
    public function get($name)
    {
        if (!isset($this->storages[$name])) {
            throw new InvalidArgumentException("Storage $name not found.");
        }

        return $this->storages[$name];
    }

    /**
     * @return StorageInterface[]
     */
    public function all()
    {
        return $this->storages;
    }
}
