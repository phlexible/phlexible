<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Volume\FileSource;

/**
 * Create file source
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CreateFileSource implements FileSourceInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $mimeType;

    /**
     * @var int
     */
    private $size;

    /**
     * @param string $name
     * @param string $mimeType
     * @param int    $size
     */
    public function __construct($name, $mimeType = 'application/x-empty', $size = 0)
    {
        $this->name = $name;
        $this->mimeType = $mimeType;
        $this->size = $size;
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
