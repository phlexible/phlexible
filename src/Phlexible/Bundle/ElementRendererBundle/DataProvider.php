<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementRendererBundle;

use Phlexible\Bundle\ElementRendererBundle\Event\ProvideEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Data provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DataProvider implements DataProviderInterface
{
    /**
     * @var DataProviderInterface
     */
    private $dataProvider;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param DataProviderInterface    $dataProvider
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(DataProviderInterface $dataProvider, EventDispatcherInterface $dispatcher)
    {
        $this->dataProvider = $dataProvider;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function provide(RenderConfiguration $renderConfiguration)
    {
        $data = $this->dataProvider->provide($renderConfiguration);

        $event = new ProvideEvent($this, $data);
        $this->dispatcher->dispatch(ElementRendererEvents::PROVIDE, $event);

        return $data;
    }
}