<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Storage;

use Phlexible\Bundle\MediaTemplateBundle\Exception\InvalidArgumentException;

/**
 * Storage manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class StorageManager
{
    /**
     * @var StorageInterface[]
     */
    private $storages = array();

    /**
     * @param StorageInterface[] $storages
     */
    public function __construct(array $storages = array())
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
