<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaManager\Volume;

/**
 * Has meta set interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface HasMetaSetInterface
{
    /**
     * @param array $metasets
     *
     * @return $this
     */
    public function setMetasets(array $metasets);

    /**
     * @return array
     */
    public function getMetasets();

    /**
     * @param string $metaSetId
     *
     * @return $this
     */
    public function addMetaSet($metaSetId);

    /**
     * @param string $metaSetId
     *
     * @return $this
     */
    public function removeMetaSet($metaSetId);
}
