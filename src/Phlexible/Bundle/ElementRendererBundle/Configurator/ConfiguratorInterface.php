<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementRendererBundle\Configurator;

use Symfony\Component\HttpFoundation\Request;

/**
 * Configurator interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ConfiguratorInterface
{
    /**
     * @param Request       $renderRequest
     * @param Configuration $renderConfiguration
     */
    public function configure(Request $renderRequest, Configuration $renderConfiguration);
}
