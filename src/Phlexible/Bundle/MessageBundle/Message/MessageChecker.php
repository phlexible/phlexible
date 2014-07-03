<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\Message;

use Phlexible\Bundle\MessageBundle\Criteria\Criteria;
use Phlexible\Bundle\MessageBundle\Criteria\Criterium;
use Phlexible\Bundle\MessageBundle\Entity\Filter;
use Phlexible\Bundle\MessageBundle\Entity\Message;
use Phlexible\Bundle\MessageBundle\Exception\InvalidArgumentException;

/**
 * Message check
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MessageChecker
{
    /**
     * Check if message satisfies the given filter
     *
     * @param Filter  $filter
     * @param Message $message
     *
     * @return bool
     */
    public function checkByFilter(Filter $filter, Message $message)
    {
        return $this->check($filter->getCriteria(), $message);
    }

    /**
     * Check if message satisfies the given criteria
     *
     * @param Criteria $criteria
     * @param Message  $message
     *
     * @throws InvalidArgumentException
     * @return bool
     */
    public function check(Criteria $criteria, Message $message)
    {
        foreach ($criteria as $criterium) {
            if ($criterium instanceof Criteria) {
                if (!$this->check($criterium, $message)) {
                    return false;
                }

                return true;
            } elseif (!$criterium instanceof Criterium) {
                throw new InvalidArgumentException('Criterium is neither of type Criterium nor Criteria. Type is ' . gettype($criterium));
            }

            $type  = $criterium->getType();
            $value = $criterium->getValue();

            if (!strlen($value)) {
                continue;
            }

            switch ($type)
            {
                case Criteria::CRITERIUM_SUBJECT_LIKE:
                    if (stristr($message->getSubject(), $value) === false) {
                        continue 2;
                    }
                    break;

                case Criteria::CRITERIUM_SUBJECT_NOT_LIKE:
                    if (stristr($message->getSubject(), $value) !== false) {
                        continue 2;
                    }
                    break;

                case Criteria::CRITERIUM_BODY_LIKE:
                    if (stristr($message->getBody(), $value) === false) {
                        continue 2;
                    }
                    break;

                case Criteria::CRITERIUM_BODY_NOT_LIKE:
                    if (stristr($message->getBody(), $value) !== false) {
                        continue 2;
                    }
                    break;

                case Criteria::CRITERIUM_PRIORITY_IS:
                    if ($message->getPriority() != $value) {
                        continue 2;
                    }
                    break;

                case Criteria::CRITERIUM_PRIORITY_MIN:
                    if ($message->getPriority() < $value) {
                        continue 2;
                    }
                    break;

                case Criteria::CRITERIUM_PRIORITY_IN:
                    if (!in_array($message->getPriority(), $value)) {
                        continue 2;
                    }
                    break;

                case Criteria::CRITERIUM_TYPE_IS:
                    if ($message->getType() != $value) {
                        continue 2;
                    }
                    break;

                case Criteria::CRITERIUM_TYPE_IN:
                    if (!in_array($message->getType(), $value)) {
                        continue 2;
                    }
                    break;

                case Criteria::CRITERIUM_CHANNEL_IS:
                    if ($message->getChannel() != $value) {
                        continue 2;
                    }
                    break;

                case Criteria::CRITERIUM_CHANNEL_LIKE:
                    if (stristr($message->getChannel(), $value) === false) {
                        continue 2;
                    }
                    break;

                case Criteria::CRITERIUM_CHANNEL_IN:
                    if (!in_array($message->getChannel(), $value)) {
                        continue 2;
                    }
                    break;

                case Criteria::CRITERIUM_RESOURCE_IS:
                    if ($message->getResource() != $value) {
                        continue 2;
                    }
                    break;

                case Criteria::CRITERIUM_RESOURCE_IN:
                    if (!in_array($message->getResource(), $value)) {
                        continue 2;
                    }
                    break;

                case Criteria::CRITERIUM_MAX_AGE:
                    if ($message->getCreatedAt()->format('U') < (time() - ($value * 24 * 60 * 60))) {
                        continue 2;
                    }
                    break;

                case Criteria::CRITERIUM_MIN_AGE:
                    if ($message->getCreatedAt()->format('U') > (time() - ($value * 24 * 60 * 60))) {
                        continue 2;
                    }
                    break;

                case Criteria::CRITERIUM_START_DATE:
                    if ($message->getCreatedAt()->format('U') < strtotime($value)) {
                        continue 2;
                    }
                    break;

                case Criteria::CRITERIUM_END_DATE:
                    if ($message->getCreatedAt()->format('U') > strtotime($value)) {
                        continue 2;
                    }
                    break;

                case Criteria::CRITERIUM_DATE_IS:
                    if ($message->getCreatedAt()->format('Y-m-d') !== date('Y-m-d', strtotime($value))) {
                        continue 2;
                    }
                    break;

                default:
                    continue 2;
                    break;
            }

            return true;
        }

        return false;
    }
}
