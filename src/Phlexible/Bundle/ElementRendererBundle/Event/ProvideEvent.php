<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementRendererBundle\Event;

use Phlexible\Bundle\ElementRendererBundle\DataProvider\DataProvider;
use Symfony\Component\EventDispatcher\Event;

/**
 * Provide event
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class ProvideEvent extends Event
{
    /**
     * @var \Phlexible\Bundle\ElementRendererBundle\DataProvider\DataProvider
     */
    private $dataProvider = null;

    /**
     * @var \ArrayObject
     */
    private $data = null;

    /**
     * @param DataProvider $dataProvider
     * @param \ArrayObject $data
     */
    public function __construct(DataProvider $dataProvider, \ArrayObject $data)
    {
        $this->dataProvider = $dataProvider;
        $this->data = $data;
    }

    /**
     * Return data provider
     *
     * @return \Phlexible\Bundle\ElementRendererBundle\DataProvider\DataProvider
     */
    public function getDataProvider()
    {
        return $this->dataProvider;
    }

    /**
     * Return data
     *
     * @return \ArrayObject
     */
    public function getData()
    {
        return $this->data;
    }
}