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

use Phlexible\Component\Mime\Adapter\AdapterInterface;

/**
 * This class provides automatic mimetype detection functionality.
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class MimeDetector
{
    const RETURN_INTERNETMEDIATYPE = 'internetmediatype';
    const RETURN_SPLFILEINFO = 'splfileinfo';
    const RETURN_STRING = 'string';

    /**
     * @var AdapterInterface
     */
    private $adapter = null;

    /**
     * @param AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter = null)
    {
        if (null !== $adapter) {
            $this->setAdapter($adapter);
        }
    }

    /**
     * Set adapter.
     *
     * @param AdapterInterface $adapter
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Return adapter.
     *
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Detect the internet media type for the given file.
     *
     * @param string $filename
     * @param string $return
     *
     * @return SplFileInfo|InternetMediaType|string|null
     */
    public function detect($filename, $return = self::RETURN_SPLFILEINFO)
    {
        if (null === $this->adapter) {
            return null;
        }

        if (!$this->adapter->isAvailable($filename)) {
            return null;
        }

        try {
            $mimeType = $this->getAdapter()->getInternetMediaTypeStringFromFile($filename);
        } catch (\Exception $e) {
            return null;
        }

        switch ($return) {
            case self::RETURN_STRING:
                return (string) $mimeType;

            case self::RETURN_INTERNETMEDIATYPE:
                $internetMediaType = $this->parseInternetMediaTypeString($mimeType);

                return $internetMediaType;

            case self::RETURN_SPLFILEINFO:
            default:
                $splFileInfo = new SplFileInfo($filename);
                $internetMediaType = null;
                if ($mimeType) {
                    $internetMediaType = $this->parseInternetMediaTypeString($mimeType);
                }
                $splFileInfo
                    ->setMimeType($mimeType)
                    ->setInternetMediaType($internetMediaType);

                return $splFileInfo;
        }
    }

    /**
     * Parse string to internet media type.
     *
     * @param string $internetMediaTypeString
     *
     * @return InternetMediaType
     */
    protected function parseInternetMediaTypeString($internetMediaTypeString)
    {
        $parts = explode(';', $internetMediaTypeString);

        $internetMediaTypeString = $parts[0];

        $parameters = [];
        if (!empty($parts[1])) {
            $parameterParts = explode(' ', trim($parts[1]));
            $parameters = [];
            foreach ($parameterParts as $parameterPart) {
                $attributeValueParts = explode('=', trim($parameterPart));

                $attribute = trim($attributeValueParts[0]);
                $attributeValue = trim($attributeValueParts[1]);
                $parameters[$attribute] = $attributeValue;
            }
        }

        $parts = explode('/', $internetMediaTypeString);
        $type = $parts[0];
        $subtype = null;
        if (!empty($parts[1])) {
            $subtype = $parts[1];
        }

        $internetMediaType = new InternetMediaType($type, $subtype, $parameters);

        return $internetMediaType;
    }
}
