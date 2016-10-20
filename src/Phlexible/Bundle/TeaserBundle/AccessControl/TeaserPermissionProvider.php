<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TeaserBundle\AccessControl;

use Phlexible\Component\AccessControl\Permission\Permission;
use Phlexible\Component\AccessControl\Permission\PermissionCollection;
use Phlexible\Component\AccessControl\Permission\PermissionProviderInterface;

/**
 * Teaser permission provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TeaserPermissionProvider implements PermissionProviderInterface
{
    /**
     * @var string
     */
    private $objectType;

    /**
     * @param string $objectType
     */
    public function __construct($objectType = 'Phlexible\Bundle\TeaserBundle\Entity\Teaser')
    {
        $this->objectType = $objectType;
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissions()
    {
        return new PermissionCollection($this->objectType, array(
            new Permission('VIEW', 1),
            new Permission('EDIT', 2),
            new Permission('CREATE', 4),
            new Permission('DELETE', 8),
            new Permission('PUBLISH', 16),
            new Permission('ACCESS', 32),
        ));
    }
}
