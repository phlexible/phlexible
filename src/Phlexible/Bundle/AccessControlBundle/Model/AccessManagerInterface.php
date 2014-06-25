<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\AccessControlBundle\Model;

/**
 * Access manager interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface AccessManagerInterface
{
    /**
     * @param array $criteria
     *
     * @return array
     * @throws \Exception
     */
    public function findBy(array $criteria);

    /**
     * @param string  $rightType
     * @param string  $contentType
     * @param string  $contentId
     * @param string  $objectType
     * @param string  $objectId
     * @param string  $right
     * @param integer $inherit
     * @param string  $language
     *
     * @return $this
     */
    public function setRight($rightType, $contentType, $contentId, $objectType, $objectId, $right, $inherit = 1, $language = null);

    /**
     * @param string $rightType
     * @param string $contentType
     * @param string $contentId
     * @param string $objectType
     * @param string $objectId
     * @param string $right
     * @param string $language
     *
     * @return $this
     */
    public function removeRight($rightType, $contentType, $contentId, $objectType = null, $objectId = null, $right = null, $language = null);
}
