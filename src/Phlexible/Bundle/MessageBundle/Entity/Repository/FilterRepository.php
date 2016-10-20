<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MessageBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\MessageBundle\Entity\Filter;
use Phlexible\Bundle\MessageBundle\Entity\Message;
use Phlexible\Bundle\MessageBundle\Message\MessageChecker;

/**
 * Filter repository
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FilterRepository extends EntityRepository
{
    /**
     * @return Filter
     */
    public function create()
    {
        return new Filter();
    }

    /**
     * Return one filter for a user by title
     *
     * @param string $userId
     * @param string $title
     *
     * @return Filter
     */
    public function findOneByUserIdAndTitle($userId, $title)
    {
        return $this->findOneBy(['userId' => $userId, 'title' => $title]);
    }

    /**
     * Return all filters applicable for a message
     *
     * @param Message $message
     * @param string  $handler
     *
     * @return Filter[]
     */
    public function findApplicableFiltersByMessage(Message $message, $handler = null)
    {
        if ($handler) {
            $filters = $this->findByHandler($handler);
        } else {
            $filters = $this->findAll();
        }

        $applicableFilters = [];

        $checker = new MessageChecker();
        foreach ($filters as $filter) {
            if (!$checker->checkByFilter($filter, $message)) {
                continue;
            }

            $applicableFilters[] = $filter;
        }

        return $applicableFilters;
    }
}
