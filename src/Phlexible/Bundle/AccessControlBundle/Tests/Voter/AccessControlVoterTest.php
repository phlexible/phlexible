<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\phlexible\src\Phlexible\Bundle\AccessControlBundle\Tests\Voter;

use Phlexible\Bundle\AccessControlBundle\Voter\AccessControlVoter;
use Phlexible\Bundle\UserBundle\Entity\User;
use Phlexible\Component\AccessControl\Domain\AccessControlList;
use Phlexible\Component\AccessControl\Domain\Entry;
use Phlexible\Component\AccessControl\Model\AccessManagerInterface;
use Phlexible\Component\AccessControl\Model\ObjectIdentityInterface;
use Phlexible\Component\AccessControl\Permission\Permission;
use Phlexible\Component\AccessControl\Permission\PermissionCollection;
use Phlexible\Component\AccessControl\Permission\PermissionRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class TestObject implements ObjectIdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 123;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return get_class($this);
    }
}

/**
 * Access control voter test.
 *
 * @covers \Phlexible\Bundle\AccessControlBundle\Voter\AccessControlVoter
 */
class AccessControlVoterTest extends TestCase
{
    public function testVoteIsAbstainedForNonDomainObject()
    {
        $object = new \stdClass();
        $permissionRegistry = new PermissionRegistry();

        $accessManager = $this->prophesize(AccessManagerInterface::class);

        $voter = new AccessControlVoter($accessManager->reveal(), $permissionRegistry, false);

        $result = $voter->vote(
            new PreAuthenticatedToken(new User(), 'username', 'test', array('ROLE_USER')),
            $object,
            array('VIEW')
        );

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    public function testVoteIsAbstainedWithMissingPermissions()
    {
        $object = new TestObject();
        $permissionRegistry = new PermissionRegistry();

        $accessManager = $this->prophesize(AccessManagerInterface::class);

        $voter = new AccessControlVoter($accessManager->reveal(), $permissionRegistry, false);

        $result = $voter->vote(
            new PreAuthenticatedToken(new User(), 'username', 'test', array('ROLE_USER')),
            $object,
            array('VIEW')
        );

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    public function testVoteIsAbstainedWithMissingPermission()
    {
        $object = new TestObject();
        $permissionRegistry = new PermissionRegistry(array(new PermissionCollection('stdClass')));

        $accessManager = $this->prophesize(AccessManagerInterface::class);

        $voter = new AccessControlVoter($accessManager->reveal(), $permissionRegistry, false);

        $result = $voter->vote(
            new PreAuthenticatedToken(new User(), 'username', 'test', array('ROLE_USER')),
            $object,
            array('VIEW')
        );

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    /**
     * @return $this
     */
    private function createUser()
    {
        $user = new User();

        return $user->setId('testUser');
    }

    /**
     * @param $object
     *
     * @return PermissionCollection
     */
    private function createPermissions($object)
    {
        return new PermissionCollection(
            $object->getType(),
            array(
                new Permission('VIEW', 1),
                new Permission('EDIT', 2),
                new Permission('DELETE', 4),
            )
        );
    }

    public function testVoteIsDeniedOnUnknownAclWithUnpermissiveStrategy()
    {
        $object = new TestObject();
        $user = $this->createUser();
        $permissions = $this->createPermissions($object);
        $permissionRegistry = new PermissionRegistry(array($permissions));

        $accessManager = $this->prophesize(AccessManagerInterface::class);
        $accessManager->findAcl($object)->willReturn(new AccessControlList($permissions, $object));

        $voter = new AccessControlVoter($accessManager->reveal(), $permissionRegistry, false);

        $result = $voter->vote(
            new PreAuthenticatedToken($user, 'username', 'test', array('ROLE_USER')),
            $object,
            array('VIEW')
        );

        $this->assertSame(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testVoteIsGrantedOnUnknownAclWithPermissiveStrategy()
    {
        $object = new TestObject();
        $user = $this->createUser();
        $permissions = $this->createPermissions($object);
        $permissionRegistry = new PermissionRegistry(array($permissions));

        $accessManager = $this->prophesize(AccessManagerInterface::class);
        $accessManager->findAcl($object)->willReturn(new AccessControlList($permissions, $object));

        $voter = new AccessControlVoter($accessManager->reveal(), $permissionRegistry, true);

        $result = $voter->vote(
            new PreAuthenticatedToken($user, 'username', 'test', array('ROLE_USER')),
            $object,
            array('VIEW')
        );

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testVoteIsGranted()
    {
        $object = new TestObject();
        $user = $this->createUser();
        $permissions = $this->createPermissions($object);
        $acl = new AccessControlList($permissions, $object);
        $ace = new Entry($acl, get_class($object), 1, get_class($user), $user->getId(), 7, 0, 0);
        $acl->addEntry($ace);
        $permissionRegistry = new PermissionRegistry(array($permissions));

        $accessManager = $this->prophesize(AccessManagerInterface::class);
        $accessManager->findAcl($object)->willReturn($acl);

        $voter = new AccessControlVoter($accessManager->reveal(), $permissionRegistry, false);

        $result = $voter->vote(
            new PreAuthenticatedToken($user, 'cred', 'test', array('ROLE_USER')),
            $object,
            array('VIEW')
        );

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }
}
