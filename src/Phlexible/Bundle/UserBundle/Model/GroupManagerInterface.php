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

use FOS\UserBundle\Model\GroupInterface;

/**
 * Group manager interface.
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
     * @return GroupInterface
     */
    public function create();

    /**
     * Find group.
     *
     * @param int $groupId
     *
     * @return GroupInterface
     */
    public function find($groupId);

    /**
     * Find all groups.
     *
     * @return GroupInterface[]
     */
    public function findAll();

    /**
     * Find group by name.
     *
     * @param string $name
     *
     * @return GroupInterface
     */
    public function findByName($name);

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return GroupInterface[]
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null);

    /**
     * @param array $criteria
     * @param array $order
     *
     * @return GroupInterface
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
     * @param GroupInterface $group
     */
    public function updateGroup(GroupInterface $group);

    /**
     * @param GroupInterface $group
     */
    public function reloadGroup(GroupInterface $group);

    /**
     * @param GroupInterface $group
     */
    public function deleteGroup(GroupInterface $group);
}
