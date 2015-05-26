<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\AccessControl\ContentObject;

/**
 * Content object interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ContentObjectInterface
{
    /**
     * Return content object identifier
     *
     * @return array
     */
    public function getContentObjectIdentifiers();

    /**
     * Return content object path
     *
     * @return array
     */
    public function getContentObjectPath();
}
