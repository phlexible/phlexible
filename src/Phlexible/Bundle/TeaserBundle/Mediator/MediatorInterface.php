<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TeaserBundle\Mediator;

use Phlexible\Bundle\TeaserBundle\Entity\Teaser;

/**
 * Mediator interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface MediatorInterface
{
    /**
     * @param Teaser $teaser
     *
     * @return bool
     */
    public function accept(Teaser $teaser);

    /**
     * @param Teaser $teaser
     * @param string $field
     * @param string $language
     *
     * @return string
     */
    public function getTitle(Teaser $teaser, $field, $language);

    /**
     * @param Teaser $teaser
     *
     * @return string
     */
    public function getUniqueId(Teaser $teaser);

    /**
     * @param Teaser $teaser
     *
     * @return mixed
     */
    public function getObject(Teaser $teaser);

    /**
     * @param Teaser $teaser
     *
     * @return mixed
     */
    public function getVersionedObject(Teaser $teaser);
}
