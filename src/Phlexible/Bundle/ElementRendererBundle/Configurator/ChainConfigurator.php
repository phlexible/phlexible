<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementRendererBundle\Configurator;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Element render configurator
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
     * @param ConfiguratorInterface[]  $configurators
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
