<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementRendererBundle\DataProvider;

use Phlexible\Bundle\ElementRendererBundle\Configurator\RenderConfigurator;
use Phlexible\Bundle\ElementRendererBundle\ElementRendererEvents;
use Phlexible\Bundle\ElementRendererBundle\Event\ProvideEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Data provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DataProvider
{
    /**
     * @var \Phlexible\Bundle\ElementRendererBundle\Configurator\RenderConfigurator
     */
    private $configurator;

    /**
     * @var DataProviderInterface[]
     */
    private $dataProviders;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param \Phlexible\Bundle\ElementRendererBundle\RenderConfigurator\\Phlexible\Bundle\ElementRendererBundle\Configurator\RenderConfigurator       $configurator
     * @param EventDispatcherInterface $dispatcher
     * @param DataProviderInterface[]  $dataProviders
     */
    public function __construct(RenderConfigurator $configurator, EventDispatcherInterface $dispatcher, array $dataProviders = array())
    {
        $this->configurator = $configurator;
        $this->dispatcher = $dispatcher;

        foreach ($dataProviders as $dataProvider) {
            $this->addDataProvider($dataProvider);
        }
    }

    /**
     * @param DataProviderInterface $dataProvider
     *
     * @return $this
     */
    public function addDataProvider(DataProviderInterface $dataProvider)
    {
        $this->dataProviders[] = $dataProvider;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function provide(Request $request)
    {
        $configuration = $this->configurator->configure($request);

        $dataProvider = current($this->dataProviders);
        $data = $dataProvider->provide($configuration);

        $event = new ProvideEvent($this, $data);
        $this->dispatcher->dispatch(ElementRendererEvents::PROVIDE, $event);

        return $data;
    }
}