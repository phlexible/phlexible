<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\FileSource;

use Phlexible\Bundle\MediaSiteBundle\Exception\InvalidArgumentException;

/**
 * Stream file source
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class StreamFileSource implements StreamSourceInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var resource
     */
    private $stream;

    /**
     * @var string
     */
    private $mimeType;

    /**
     * @param string   $name
     * @param resource $stream
     * @param string   $mimeType
     * @param int      $size
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($name, $stream, $mimeType, $size)
    {
        if (!is_resource($stream)) {
            throw new InvalidArgumentException('Not a stream.');
        }

        $this->name = $name;
        $this->stream = $stream;
        $this->mimeType = $mimeType;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * {@inheritdoc}
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }
}