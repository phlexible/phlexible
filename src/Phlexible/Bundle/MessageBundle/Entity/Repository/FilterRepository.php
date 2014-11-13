<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
        return $this->findOneBy(array('userId' => $userId, 'title' => $title));
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

        $applicableFilters = array();

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
