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
     * Return all data source titles
     *
     * @param bool $sorted
     *
     * @return array
     */
    public function getAllDataSourceTitles($sorted = false);

    /**
     * Return all data source languages
     *
     * @param string $dataSourceId
     *
     * @return array
     */
    public function getAllDataSourceLanguages($dataSourceId);

    /**
     * Return all data source ids
     *
     * @return array
     */
    public function getAllDataSourceIds();

    /**
     * @param string    $sourceId
     * @param string    $language
     * @param null|bool $isActive
     *
     * @return array
     */
    public function getAllValuesByDataSourceId($sourceId, $language, $isActive = null);

    /**
     * @param DataSource $dataSource
     */
    public function updateDataSource(DataSource $dataSource);

    /**
     * @param DataSource $dataSource
     */
    public function deleteDataSource(DataSource $dataSource);
}