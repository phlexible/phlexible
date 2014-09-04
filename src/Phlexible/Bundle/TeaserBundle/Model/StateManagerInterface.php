<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
