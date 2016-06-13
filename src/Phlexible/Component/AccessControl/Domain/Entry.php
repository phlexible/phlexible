<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\AccessControl\Domain;

use Phlexible\Component\AccessControl\Model\EntryInterface;

/**
 * Access control entry
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Entry implements EntryInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var AccessControlList
     */
    private $acl;

    /**
     * @var string
     */
    private $objectType;

    /**
     * @var string
     */
    private $objectIdentifier;

    /**
     * @var string
     */
    private $securityType;

    /**
     * @var string
     */
    private $securityIdentifier;

    /**
     * @var int
     */
    private $mask;

    /**
     * @var int
     */
    private $stopMask;

    /**
     * @var int
     */
    private $noInheritMask;

    /**
     * @param AccessControlList $acl
     * @param string            $objectType
     * @param string            $objectIdentifier
     * @param string            $securityType
     * @param string            $securityIdentifier
     * @param bool              $mask
     * @param bool              $stopMask
     * @param bool              $noInheritMask
     */
    public function __construct(AccessControlList $acl, $objectType, $objectIdentifier, $securityType, $securityIdentifier, $mask, $stopMask, $noInheritMask)
    {
        $this->acl = $acl;
        $this->objectType = $objectType;
        $this->objectIdentifier = $objectIdentifier;
        $this->securityType = $securityType;
        $this->securityIdentifier = $securityIdentifier;
        $this->mask = $mask;
        $this->stopMask = $stopMask;
        $this->noInheritMask = $noInheritMask;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAcl()
    {
        return $this->acl;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectType()
    {
        return $this->objectType;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectIdentifier()
    {
        return $this->objectIdentifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getSecurityType()
    {
        return $this->securityType;
    }

    /**
     * {@inheritdoc}
     */
    public function getSecurityIdentifier()
    {
        return $this->securityIdentifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getMask()
    {
        return $this->mask;
    }

    /**
     * {@inheritdoc}
     */
    public function setMask($mask)
    {
        $this->mask = $mask;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStopMask()
    {
        return $this->stopMask;
    }

    /**
     * {@inheritdoc}
     */
    public function setStopMask($stopMask)
    {
        $this->stopMask = $stopMask;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getNoInheritMask()
    {
        return $this->noInheritMask;
    }

    /**
     * {@inheritdoc}
     */
    public function setNoInheritMask($inheritMask)
    {
        $this->noInheritMask = $inheritMask;

        return $this;
    }
}
