<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Event;

use Phlexible\Bundle\GuiBundle\Config\Config;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Get config event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GetConfigEvent extends Event
{
    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param SecurityContextInterface $securityContext
     * @param Config                   $config
     */
    public function __construct(SecurityContextInterface $securityContext, Config $config)
    {
        $this->securityContext = $securityContext;
        $this->config = $config;
    }

    /**
     * @return SecurityContextInterface
     */
    public function getSecurityContext()
    {
        return $this->securityContext;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }
}