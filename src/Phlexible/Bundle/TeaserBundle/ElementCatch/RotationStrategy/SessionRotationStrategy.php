<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\ElementCatch\RotationStrategy;

use Phlexible\Bundle\TeaserBundle\ElementCatch\ElementCatchResultPool;
use Zend_Session_Namespace as Session;

/**
 * Cache rotations strategy
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SessionRotationStrategy implements RotationStrategyInterface
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastRotationPosition(ElementCatchResultPool $pool)
    {
        $identifier = $pool->getIdentifier();

        $position = $this->session->$identifier;

        if (!isset($position) || !$position) {
            $this->session->$identifier = $position = 0;
        }

        return  (integer) $position;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastRotationPosition(ElementCatchResultPool $pool, $position)
    {
        $identifier = $pool->getIdentifier();

        $this->session->$identifier = $position;

        return $this;
    }
}