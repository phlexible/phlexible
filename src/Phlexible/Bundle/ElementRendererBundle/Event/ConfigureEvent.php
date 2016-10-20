<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementRendererBundle\Event;

use Phlexible\Bundle\ElementRendererBundle\Configurator\Configuration;
use Symfony\Component\EventDispatcher\Event;

/**
 * Configure event
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class ConfigureEvent extends Event
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
}
