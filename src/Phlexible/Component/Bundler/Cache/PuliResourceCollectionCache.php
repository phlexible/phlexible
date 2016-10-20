<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\GuiAsset\Cache;

use Puli\Repository\Resource\FileResource;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Puli resource collection cache
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PuliResourceCollectionCache
{
    /**
     * @var string
     */
    private $file;

    /**
     * @var boolean
     */
    private $debug;

    /**
     * @param string $file
     * @param bool   $debug
     */
    public function __construct($file, $debug)
    {
        $this->file = $file;
        $this->debug = $debug;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->file;
    }

    /**
     * @param FileResource[] $resources
     *
     * @return bool
     */
    public function isFresh(array $resources)
    {
        if (!file_exists($this->file)) {
            return false;
        }

        $timestamp = filemtime($this->file);

        foreach ($resources as $resource) {
            if ($resource instanceof FileResource && $timestamp < $resource->getMetadata()->getModificationTime()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $content
     */
    public function write($content)
    {
        $mode = 0666;
        $umask = umask();

        $filesystem = new Filesystem();
        $filesystem->dumpFile($this->file, $content, null);

        try {
            $filesystem->chmod($this->file, $mode, $umask);
        } catch (IOException $e) {
            // discard chmod failure (some filesystem may not support it)
        }
    }

    /**
     * Gets the cache file path.
     *
     * @return string The cache file path
     */
    public function getPath()
    {
        return $this->file;
    }
}
