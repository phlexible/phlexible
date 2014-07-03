<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Mediator;

interface Versioned
{
    public function getOnlineVersion();

    public function getLatestVersion();

    public function getAttributesForVersion();
}

interface Languaged
{
    /**
     * @return array
     */
    public function getContentForLanguage($language);
}

/**
 * Mediator interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface MediatorInterface
{
    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getBackendTitle();

    /**
     * @return string
     */
    public function getNavigationTitle();

    /**
     * @return string
     */
    public function getPageTitle();

    /**
     * @return mixed
     */
    public function getAttributes();
}
