<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Model;

use Phlexible\Bundle\TeaserBundle\Entity\Teaser;

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
     * @param string $language
     *
     * @return array
     */
    public function getPublishInfo(Teaser $teaser, $language);

    /**
     * @param Teaser $teaser
     * @param string $language
     *
     * @return bool
     */
    public function isAsync(Teaser $teaser, $language);
}
