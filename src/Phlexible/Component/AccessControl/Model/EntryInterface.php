<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\AccessControl\Model;

use Phlexible\Component\AccessControl\Domain\AccessControlList;

/**
 * Access control entry.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface EntryInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return AccessControlList
     */
    public function getAcl();

    /**
     * @return string
     */
    public function getSecurityType();

    /**
     * @return string
     */
    public function getSecurityIdentifier();

    /**
     * @return int
     */
    public function getMask();

    /**
     * @param int $mask
     *
     * @return $this
     */
    public function setMask($mask);

    /**
     * @return int
     */
    public function getStopMask();

    /**
     * @param int $stopMask
     *
     * @return $this
     */
    public function setStopMask($stopMask);

    /**
     * @return int
     */
    public function getNoInheritMask();

    /**
     * @param int $inheritMask
     *
     * @return $this
     */
    public function setNoInheritMask($inheritMask);
}
