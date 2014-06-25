<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\SelectFieldProvider;

/**
 * Select field provider interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface SelectFieldProviderInterface
{
    /**
     * Return title for this select provider
     *
     * @return string
     */
    public function getTitle();

    /**
     * Return associative data for this select provider
     *
     * @param string $language
     *
     * @return array
     */
    public function get($language);
}