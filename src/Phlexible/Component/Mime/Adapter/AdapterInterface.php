<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Mime\Adapter;

/**
 * Internet media type detector adapter interface.
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
interface AdapterInterface
{
    /**
     * Check if this adapter is available.
     *
     * @param string $filename
     *
     * @return bool
     */
    public function isAvailable($filename);

    /**
     * Return internet media type string from file.
     *
     * @param string $filename
     *
     * @return string
     */
    public function getInternetMediaTypeStringFromFile($filename);
}
