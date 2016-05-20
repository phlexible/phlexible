<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\Model;

use Phlexible\Bundle\DataSourceBundle\Entity\DataSource;

/**
 * Data source manager interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface DataSourceManagerInterface
{
    /**
     * @param string $id
     *
     * @return DataSource
     */
    public function find($id);

    /**
     * @param array      $criteria
     * @param null|array $orderBy
     * @param null|int   $limit
     * @param null|int   $offset
     *
     * @return array|DataSource[]
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null);

    /**
     * @param DataSource $dataSource
     * @param bool       $flush
     */
    public function updateDataSource(DataSource $dataSource, $flush = true);

    /**
     * @param DataSource $dataSource
     */
    public function deleteDataSource(DataSource $dataSource);
}
