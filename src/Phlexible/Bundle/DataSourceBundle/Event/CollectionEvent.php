<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\Event;

use Phlexible\Bundle\DataSourceBundle\Entity\DataSource;
use Phlexible\Bundle\DataSourceBundle\ValueCollection;
use Symfony\Component\EventDispatcher\Event;

/**
 * Mark inactive
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class CollectionEvent extends Event
{
    /**
     * @var DataSource
     */
    private $dataSource;

    /**
     * @var ValueCollection
     */
    private $collection;

    /**
     * @param DataSource      $dataSource
     * @param ValueCollection $collection
     */
    public function __construct(DataSource $dataSource,
                                ValueCollection $collection)
    {
        $this->dataSource = $dataSource;
        $this->collection = $collection;
    }

    /**
     * @return DataSource
     */
    public function getDataSource()
    {
        return $this->dataSource;
    }

    /**
     * @return ValueCollection
     */
    public function getCollection()
    {
        return $this->collection;
    }
}