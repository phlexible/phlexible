<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Usage;

/**
 * File usage
 *
 * @author Matthias Harmuth <mharmuth@brainbits.net>
 */
class FileUsage implements UsageInterface
{
    const TYPE_ELEMENT = 'element';
    const TYPE_LDAP    = 'ldap';

    /**
     * @var string
     */
    private $fileId;

    /**
     * @var int
     */
    private $fileVersion;

    /**
     * @var string
     */
    private $usageType;

    /**
     * @var string
     */
    private $usageId;

    /**
     * @var int
     */
    private $status;

    /**
     * @param string $fileId
     * @param int    $fileVersion
     * @param string $usageType
     * @param string $usageId
     * @param int    $status
     */
    public function __construct($fileId, $fileVersion, $usageType, $usageId, $status)
    {
        $this->fileId = $fileId;
        $this->fileVersion = $fileVersion;
        $this->usageType = $usageType;
        $this->usageId = $usageId;
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getFileId()
    {
        return $this->fileId;
    }

    /**
     * @return int
     */
    public function getFileVersion()
    {
        return $this->fileVersion;
    }

    /**
     * @return string
     */
    public function getUsageType()
    {
        return $this->usageType;
    }

    /**
     * @return string
     */
    public function getUsageId()
    {
        return $this->usageId;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Return array represenattion of this usage
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'fileId'      => $this->fileId,
            'fileVersion' => $this->fileVersion,
            'usageType'   => $this->usageType,
            'usageId'     => $this->usageId,
            'status'      => $this->status,
        );
    }
}
