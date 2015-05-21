<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Elementtype\SelectFieldProvider;

/**
 * Select field provider interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface SelectFieldProviderInterface
{
    /**
     * Return name of this provider
     *
     * @return string
     */
    public function getName();

    /**
     * Return title for this provider
     *
     * @param string $language
     *
     * @return string
     */
    public function getTitle($language);

    /**
     * Return associative data for this provider
     *
     * @param string $language
     *
     * @return array
     */
    public function getData($language);
}
