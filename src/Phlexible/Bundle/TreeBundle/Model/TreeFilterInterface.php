<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Model;

/**
 * Tree filter.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface TreeFilterInterface
{
    /**
     * Set filter values.
     *
     * @param array $filterValues
     *
     * @return $this
     */
    public function setFilterValues(array $filterValues);

    /**
     * @return $this
     */
    public function reset();

    /**
     * @param int $id
     *
     * @return array
     */
    public function getPager($id);

    /**
     * Set sort mode.
     *
     * @param string $sortMode
     *
     * @return $this
     */
    public function setSortMode($sortMode);

    /**
     * @param int $limit
     * @param int $start
     *
     * @return array
     */
    public function getIds($limit = null, $start = null);

    /**
     * Return filter values.
     *
     * @return array
     */
    public function getFilterValues();

    /**
     * Set sort dir.
     *
     * @param string $sortDir
     *
     * @return $this
     */
    public function setSortDir($sortDir);

    /**
     * Return sort mode.
     *
     * @return string
     */
    public function getSortMode();

    /**
     * Return sort dir.
     *
     * @return string
     */
    public function getSortDir();

    /**
     * @return int
     */
    public function getCount();
}
