<?php
/**
 * Created by PhpStorm.
 * User: swentz
 * Date: 12.05.14
 * Time: 10:47
 */
namespace Phlexible\Bundle\TeaserBundle\ElementCatch\RotationStrategy;

use Phlexible\Bundle\TeaserBundle\ElementCatch\ElementCatchResultPool;


/**
 * Cache rotations strategy
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface RotationStrategyInterface
{
    /**
     * Get last remembered position for teaser rotation.
     *
     * @param ElementCatchResultPool $pool
     * @param int                    $position
     *
     * @return $this
     */
    public function setLastRotationPosition(ElementCatchResultPool $pool, $position);

    /**
     * Get last remembered position for teaser rotation.
     *
     * @param ElementCatchResultPool $pool
     *
     * @return int
     */
    public function getLastRotationPosition(ElementCatchResultPool $pool);
}