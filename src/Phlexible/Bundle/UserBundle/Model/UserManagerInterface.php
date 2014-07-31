<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Model;

use Phlexible\Bundle\UserBundle\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * User manager interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface UserManagerInterface
{
    /**
     * @return string
     */
    public function getUserClass();

    /**
     * @return User
     */
    public function create();

    /**
     * Find user
     *
     * @param int $userId
     *
     * @return User
     */
    public function find($userId);

    /**
     * Find all users
     *
     * @return User[]
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
     * @return User
     */
    public function findByUsername($username);

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return User[]
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
     * @return User
     */
    public function findOneBy(array $criteria, $order = array());

    /**
     * @param string $term
     *
     * @return UserInterface[]
     */
    public function search($term);

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
     * @return User[]
     */
    public function findLoggedInUsers();

    /**
     * @param string $username
     *
     * @return bool
     */
    public function checkUsername($username);

    /**
     * @param string $email
     *
     * @return bool
     */
    public function checkEmail($email);

    /**
     * {@inheritDoc}
     */
    public function updatePassword(UserInterface $user);

    /**
     * @param UserInterface $user
     */
    public function updateUser(UserInterface $user);

    /**
     * @param UserInterface $user
     */
    public function reloadUser(UserInterface $user);

    /**
     * @param UserInterface $user
     * @param UserInterface $successorUser
     */
    public function deleteUser(UserInterface $user, UserInterface $successorUser);
}
