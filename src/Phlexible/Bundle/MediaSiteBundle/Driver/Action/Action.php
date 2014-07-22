<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Driver\Action;

/**
 * File action
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class Action implements ActionInterface
{
    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var string
     */
    private $userId;

    /**
     * @param \DateTime $date
     * @param string    $userId
     */
    public function __construct(\DateTime $date, $userId)
    {
        $this->date = $date;
        $this->userId = $userId;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
