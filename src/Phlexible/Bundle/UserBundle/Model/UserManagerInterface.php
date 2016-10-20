<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\UserBundle\Model;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface as BaseUserManagerInterface;

/**
 * User manager interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface UserManagerInterface extends BaseUserManagerInterface
{
    /**
     * Find user
     *
     * @param int $userId
     *
     * @return UserInterface
     */
    public function find($userId);

    /**
     * Find all users
     *
     * @return UserInterface[]
     */
    public function findAll();

    /**
     * @return int
     */
    public function countAll();

    /**
     * Find user by username
     *
     * @param string $username
     *
     * @return UserInterface
     */
    public function findByUsername($username);

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return UserInterface[]
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null);

    /**
     * @param array $criteria
     *
     * @return int
     */
    public function countBy(array $criteria);

    /**
     * @param array $criteria
     * @param array $order
     *
     * @return UserInterface
     */
    public function findOneBy(array $criteria, $order = []);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int   $limit
     * @param int   $offset
     *
     * @return UserInterface[]
     */
    public function search(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param array $criteria
     *
     * @return int
     */
    public function countSearch(array $criteria);

    /**
     * @return string
     */
    public function getSystemUserId();

    /**
     * @return string
     */
    public function getSystemUserName();

    /**
     * @return UserInterface
     */
    public function getSystemUser();

    /**
     * @return UserInterface[]
     */
    public function findLoggedInUsers();

    /**
     * @param UserInterface $user
     * @param UserInterface $successorUser
     */
    public function deleteUserWithSuccessor(UserInterface $user, UserInterface $successorUser);
}
