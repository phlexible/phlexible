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
 * Internet media type detector fallback adapter.
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class FallbackAdapter extends CompositeAdapter
{
    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @var AdapterInterface
     */
    protected $fallbackAdapter;

    /**
     * @var array
     */
    protected $fallbackValues = [
        'application/octet-stream',
        'application/x-zip',
        'application/zip',
        'application/msword',
        'text/plain',
        'image/vnd.adobe.photoshop',
        'application/vnd',
        'text/x-c++ charset=us-ascii',
        'text/x-c++; charset=us-ascii',
        'text/x-c++',
    ];

    /**
     * @param AdapterInterface $adapter
     * @param AdapterInterface $fallbackAdapter
     */
    public function __construct(AdapterInterface $adapter, AdapterInterface $fallbackAdapter)
    {
        $this->adapter = $adapter;
        $this->fallbackAdapter = $fallbackAdapter;
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable($filename)
    {
        if ($this->adapter->isAvailable($filename)) {
            return true;
        }

        if ($this->fallbackAdapter->isAvailable($filename)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getInternetMediaTypeStringFromFile($filename)
    {
        $internetMediaTypeString = '';

        if ($this->adapter->isAvailable($filename)) {
            $internetMediaTypeString = $this->adapter->getInternetMediaTypeStringFromFile($filename);
        }

        if (empty($internetMediaTypeString) || $this->isFallbackLookupNeeded($filename, $internetMediaTypeString)) {
            $fallbackInternetMediaTypeString = $this->fallbackAdapter->getInternetMediaTypeStringFromFile($filename);

            if ($fallbackInternetMediaTypeString) {
                $internetMediaTypeString = $fallbackInternetMediaTypeString;
            }
        }

        return $internetMediaTypeString;
    }

    /**
     * Check wether or not we have to use the fallback adapter.
     *
     * @param string $filename
     * @param string $internetMediaTypeString
     *
     * @return bool
     */
    private function isFallbackLookupNeeded($filename, $internetMediaTypeString)
    {
        if (!empty($internetMediaTypeString) && !in_array($internetMediaTypeString, $this->fallbackValues)) {
            return false;
        }

        if (!$this->fallbackAdapter->isAvailable($filename)) {
            return false;
        }

        return true;
    }
}
