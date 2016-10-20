<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Mime;

/**
 * Internet media type aware SPL file info
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class SplFileInfo extends \SplFileInfo
{
    /**
     * @var InternetMediaType
     */
    private $internetMediaType;

    /**
     * @var string
     */
    private $mimeType;

    /**
     * Set internet media type
     *
     * @param InternetMediaType $internetMediaType
     *
     * @return $this
     */
    public function setInternetMediaType($internetMediaType)
    {
        $this->internetMediaType = $internetMediaType;

        return $this;
    }

    /**
     * Return internet media type
     *
     * @return InternetMediaType
     */
    public function getInternetMediaType()
    {
        return $this->internetMediaType;
    }

    /**
     * Set mime type
     *
     * @param string $mimeType
     *
     * @return $this
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * Return mime type
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }
}
