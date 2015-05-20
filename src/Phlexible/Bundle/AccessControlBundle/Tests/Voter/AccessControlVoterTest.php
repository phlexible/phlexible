<?php

namespace Phlexible\phlexible\src\Phlexible\Bundle\AccessControlBundle\Tests\Voter;

use Phlexible\Bundle\AccessControlBundle\Entity\AccessControlEntry;
use Phlexible\Bundle\AccessControlBundle\Voter\AccessControlVoter;
use Phlexible\Bundle\UserBundle\Entity\User;
use Phlexible\Component\AccessControl\Model\AccessControlList;
use Phlexible\Component\AccessControl\Model\ObjectIdentityInterface;
use Phlexible\Component\AccessControl\Permission\Permission;
use Phlexible\Component\AccessControl\Permission\PermissionCollection;
use Phlexible\Component\AccessControl\Permission\PermissionRegistry;
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
        return 'test';
    }
}

class AccessControlVoterTest extends \PHPUnit_Framework_TestCase
{
    public function testVoteIsAbstainedForNonDomainObject()
    {
        $object = new \stdClass;
        $permissionRegistry = new PermissionRegistry();

        $accessManager = $this->prophesize('Phlexible\Component\AccessControl\Model\AccessManagerInterface');

        $voter = new AccessControlVoter($accessManager->reveal(), $permissionRegistry);

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

        $accessManager = $this->prophesize('Phlexible\Component\AccessControl\Model\AccessManagerInterface');

        $voter = new AccessControlVoter($accessManager->reveal(), $permissionRegistry);

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

        $accessManager = $this->prophesize('Phlexible\Component\AccessControl\Model\AccessManagerInterface');

        $voter = new AccessControlVoter($accessManager->reveal(), $permissionRegistry);

        $result = $voter->vote(
            new PreAuthenticatedToken(new User(), 'username', 'test', array('ROLE_USER')),
            $object,
            array('VIEW')
        );

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    public function testVoteIsDenied()
    {
        $object = new TestObject();
        $user = new User();
        $user->setId('testUser');
        $permissions = new PermissionCollection(
            'Phlexible\phlexible\src\Phlexible\Bundle\AccessControlBundle\Tests\Voter\TestObject',
            array(
                new Permission('VIEW', 1),
                new Permission('EDIT', 2),
                new Permission('DELETE', 4),
            )
        );
        $acl = new AccessControlList($permissions, $object);
        $permissionRegistry = new PermissionRegistry(array($permissions));

        $accessManager = $this->prophesize('Phlexible\Component\AccessControl\Model\AccessManagerInterface');
        $accessManager->findAcl($object)->willReturn($acl);

        $voter = new AccessControlVoter($accessManager->reveal(), $permissionRegistry);

        $result = $voter->vote(
            new PreAuthenticatedToken($user, 'username', 'test', array('ROLE_USER')),
            $object,
            array('VIEW')
        );

        $this->assertSame(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testVoteIsGranted()
    {
        $object = new TestObject();
        $user = new User();
        $user->setId('testUser');
        $permissions = new PermissionCollection(
            'Phlexible\phlexible\src\Phlexible\Bundle\AccessControlBundle\Tests\Voter\TestObject',
            array(
                new Permission('VIEW', 1),
                new Permission('EDIT', 2),
                new Permission('DELETE', 4),
            )
        );
        $acl = new AccessControlList($permissions, $object);
        $ace = new AccessControlEntry();
        $ace
            ->setMask(7)
            ->setObjectId($object->getIdentifier())
            ->setObjectType($object->getType())
            ->setSecurityId($user->getId())
            ->setSecurityType(get_class($user));
        $acl->addAccessControlEntry($ace);
        $permissionRegistry = new PermissionRegistry(array($permissions));

        $accessManager = $this->prophesize('Phlexible\Component\AccessControl\Model\AccessManagerInterface');
        $accessManager->findAcl($object)->willReturn($acl);

        $voter = new AccessControlVoter($accessManager->reveal(), $permissionRegistry);

        $result = $voter->vote(
            new PreAuthenticatedToken($user, 'cred', 'test', array('ROLE_USER')),
            $object,
            array('VIEW')
        );

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }
}
