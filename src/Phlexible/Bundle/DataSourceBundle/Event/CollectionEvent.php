<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\Event;

use Phlexible\Bundle\DataSourceBundle\Entity\DataSourceValueBag;
use Phlexible\Bundle\DataSourceBundle\Value\ValueCollection;
use Symfony\Component\EventDispatcher\Event;

/**
 * Collection event
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class CollectionEvent extends Event
{
    /**
     * @var DataSourceValueBag
     */
    private $values;

    /**
     * @var ValueCollection
     */
    private $collection;

    /**
     * @param DataSourceValueBag $values
     * @param ValueCollection    $collection
     */
    public function __construct(DataSourceValueBag $values,
                                ValueCollection $collection)
    {
        $this->values = $values;
        $this->collection = $collection;
    }

    /**
     * @return DataSourceValueBag
     */
    public function getDataSourceValueBag()
    {
        return $this->values;
    }

    /**
     * @return ValueCollection
     */
    public function getCollection()
    {
        return $this->collection;
    }
}