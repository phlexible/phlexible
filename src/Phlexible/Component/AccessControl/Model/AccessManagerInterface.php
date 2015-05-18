<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\AccessControl\Model;

use Phlexible\Bundle\AccessControlBundle\Entity\AccessControlEntry;

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
     */
    public function findBy(array $criteria);

    /**
     * @param string      $type
     * @param string      $contentType
     * @param array       $contentIdPath
     * @param array       $securityTypes
     * @param string|null $contentLanguage
     *
     * @return AccessControlEntry[]
     */
    public function findByContentIdPath(
        $type,
        $contentType,
        array $contentIdPath,
        array $securityTypes,
        $contentLanguage = null);

    /**
     * @param string      $type
     * @param string      $contentType
     * @param string      $contentId
     * @param string      $securityType
     * @param string      $securityId
     * @param string|null $contentLanguage
     *
     * @return AccessControlEntry|null
     */
    public function findOneByValues(
        $type,
        $contentType,
        $contentId,
        $securityType,
        $securityId,
        $contentLanguage = null);

    /**
     * @param string $type
     * @param string $contentType
     * @param string $contentId
     * @param string $securityType
     * @param string $securityId
     * @param string $right
     * @param int    $inherit
     * @param string $language
     *
     * @return $this
     */
    public function setRight(
        $type,
        $contentType,
        $contentId,
        $securityType,
        $securityId,
        $right,
        $inherit = 1,
        $language = null);

    /**
     * @param string $type
     * @param string $contentType
     * @param string $contentId
     * @param string $securityType
     * @param string $securityId
     * @param string $right
     * @param string $language
     *
     * @return $this
     */
    public function removeRight(
        $type,
        $contentType,
        $contentId,
        $securityType = null,
        $securityId = null,
        $right = null,
        $language = null);
}
