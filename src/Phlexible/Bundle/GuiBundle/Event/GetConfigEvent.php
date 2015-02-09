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

/**
 * Get config event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GetConfigEvent extends Event
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }
}
