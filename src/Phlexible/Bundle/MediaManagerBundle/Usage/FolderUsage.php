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
class FolderUsage implements UsageInterface
{
    const TYPE_ELEMENT = 'element';
    const TYPE_LDAP    = 'ldap';

    /**
     * @var string
     */
    private $folderId;

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
     * @param string $folderId
     * @param string $usageType
     * @param string $usageId
     * @param int    $status
     */
    public function __construct($folderId, $usageType, $usageId, $status)
    {
        $this->folderId = $folderId;
        $this->fileVersion = $fileVersion;
        $this->usageType = $usageType;
        $this->usageId = $usageId;
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getFolderId()
    {
        return $this->folderId;
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
            'folderId'  => $this->folderId,
            'usageType' => $this->usageType,
            'usageId'   => $this->usageId,
            'status'    => $this->status,
        );
    }
}
