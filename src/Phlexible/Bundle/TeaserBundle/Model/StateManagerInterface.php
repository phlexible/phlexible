<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TeaserBundle\Model;

use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TeaserBundle\Entity\TeaserOnline;

/**
 * State manager interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface StateManagerInterface
{
    /**
     * @param Teaser $teaser
     * @param string $language
     *
     * @return bool
     */
    public function isPublished(Teaser $teaser, $language);

    /**
     * @param Teaser $teaser
     *
     * @return array
     */
    public function getPublishedLanguages(Teaser $teaser);

    /**
     * @param Teaser $teaser
     *
     * @return array
     */
    public function getPublishedVersions(Teaser $teaser);

    /**
     * @param Teaser $teaser
     * @param string $language
     *
     * @return int
     */
    public function getPublishedVersion(Teaser $teaser, $language);

    /**
     * @param Teaser $teaser
     *
     * @return TeaserOnline[]
     */
    public function findByTeaser(Teaser $teaser);

    /**
     * @param Teaser $teaser
     * @param string $language
     *
     * @return TeaserOnline
     */
    public function findOneByTeaserAndLanguage(Teaser $teaser, $language);

    /**
     * @param Teaser $teaser
     * @param string $language
     *
     * @return bool
     */
    public function isAsync(Teaser $teaser, $language);

    /**
     * @param Teaser      $teaser
     * @param int         $version
     * @param string      $language
     * @param string      $userId
     * @param string|null $comment
     *
     * @return TeaserOnline
     */
    public function publish(Teaser $teaser, $version, $language, $userId, $comment = null);

    /**
     * @param Teaser $teaser
     * @param string $language
     */
    public function setOffline(Teaser $teaser, $language);
}
