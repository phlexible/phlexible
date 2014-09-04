<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
