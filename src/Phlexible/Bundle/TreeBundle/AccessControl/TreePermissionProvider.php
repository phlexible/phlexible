<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\AccessControl;

use Phlexible\Component\AccessControl\Permission\Permission;
use Phlexible\Component\AccessControl\Permission\PermissionCollection;
use Phlexible\Component\AccessControl\Permission\PermissionProviderInterface;

/**
 * Tree permission provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreePermissionProvider implements PermissionProviderInterface
{
    /**
     * @var string
     */
    private $objectType;

    /**
     * @param string $objectType
     */
    public function __construct($objectType = 'Phlexible\Bundle\TreeBundle\Entity\TreeNode')
    {
        $this->objectType = $objectType;
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissions()
    {
        return new PermissionCollection($this->objectType, [
            new Permission('VIEW', 1),
            new Permission('EDIT', 2),
            new Permission('CREATE', 4),
            new Permission('DELETE', 8),
            new Permission('PUBLISH', 16),
            new Permission('ACCESS', 32),
        ]);
    }
}
