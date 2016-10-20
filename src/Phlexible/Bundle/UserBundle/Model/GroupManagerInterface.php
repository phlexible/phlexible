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

use Phlexible\Bundle\UserBundle\Entity\Group;

/**
 * Group manager interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface GroupManagerInterface
{
    /**
     * @return string
     */
    public function getGroupClass();

    /**
     * @return Group
     */
    public function create();

    /**
     * Find group
     *
     * @param int $groupId
     *
     * @return Group
     */
    public function find($groupId);

    /**
     * Find all groups
     *
     * @return Group[]
     */
    public function findAll();

    /**
     * Find group by name
     *
     * @param string $name
     *
     * @return Group
     */
    public function findByName($name);

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return Group[]
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null);

    /**
     * @param array $criteria
     * @param array $order
     *
     * @return Group
     */
    public function findOneBy(array $criteria, $order = []);

    /**
     * @return string
     */
    public function getEveryoneGroupId();

    /**
     * @param string $name
     *
     * @return bool
     */
    public function checkName($name);

    /**
     * @param Group $group
     */
    public function updateGroup(Group $group);

    /**
     * @param Group $group
     */
    public function reloadGroup(Group $group);

    /**
     * @param Group $group
     */
    public function deleteGroup(Group $group);
}
