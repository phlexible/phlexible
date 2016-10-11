<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Asset\Cache;

use Puli\Repository\Resource\FileResource;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Filter phlexible baseurl and basepath
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ResourceCollectionCache
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
}
