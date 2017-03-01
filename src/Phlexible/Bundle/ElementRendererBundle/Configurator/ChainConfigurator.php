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
 * Element render configurator.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ChainConfigurator implements ConfiguratorInterface
{
    /**
     * @var ConfiguratorInterface[]
     */
    private $configurators = [];

    /**
     * @param ConfiguratorInterface[] $configurators
     */
    public function __construct(array $configurators = array())
    {
        foreach ($configurators as $configurator) {
            $this->addConfigurator($configurator);
        }
    }

    /**
     * @param ConfiguratorInterface $configurator
     *
     * @return $this
     */
    public function addConfigurator(ConfiguratorInterface $configurator)
    {
        $this->configurators[] = $configurator;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(Request $request, Configuration $renderConfiguration = null)
    {
        $renderConfiguration = new Configuration();
        $renderConfiguration->set('request', $request);

        foreach ($this->configurators as $configurator) {
            $configurator->configure($request, $renderConfiguration);

            if ($renderConfiguration->hasResponse()) {
                return $renderConfiguration;
            }
        }

        return $renderConfiguration;
    }
}
