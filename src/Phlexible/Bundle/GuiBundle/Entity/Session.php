<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Session
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="session")
 */
class Session
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="session_id", type="string", length=255)
     */
    private $sessionId;

    /**
     * @var string
     * @ORM\Column(name="session_value", type="text")
     */
    private $sessionValue;

    /**
     * @var int
     * @ORM\Column(name="session_time", type="integer")
     */
    private $sessionTime;

    /**
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @param string $sessionId
     *
     * @return $this
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    /**
     * @return string
     */
    public function getSessionValue()
    {
        return $this->sessionValue;
    }

    /**
     * @param string $sessionValue
     *
     * @return $this
     */
    public function setSessionValue($sessionValue)
    {
        $this->sessionValue = $sessionValue;

        return $this;
    }

    /**
     * @return int
     */
    public function getSessionTime()
    {
        return $this->sessionTime;
    }

    /**
     * @param int $sessionTime
     *
     * @return $this
     */
    public function setSessionTime($sessionTime)
    {
        $this->sessionTime = $sessionTime;

        return $this;
    }
}
