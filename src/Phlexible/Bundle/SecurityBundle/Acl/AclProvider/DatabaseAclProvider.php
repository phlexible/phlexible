<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SecurityBundle\Acl\AclProvider;

use Doctrine\ORM\EntityManager;

/**
 * Database acl provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DatabaseAclProvider implements AclProviderInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var array
     */
    private $definitions;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function provideRoles()
    {
        $this->load();

        return $this->definitions[self::DEFINITION_ROLES];
    }

    /**
     * {@inheritdoc}
     */
    public function provideResources()
    {
        $this->load();

        return $this->definitions[self::DEFINITION_RESOURCES];
    }

    /**
     * {@inheritdoc}
     */
    public function provideAllow()
    {
        $this->load();

        return $this->definitions[self::DEFINITION_ALLOW];
    }

    /**
     * {@inheritdoc}
     */
    public function provideDeny()
    {
        $this->load();

        return $this->definitions[self::DEFINITION_DENY];
    }

    /**
     * Load definitions
     */
    private function load()
    {
        if (null !== $this->definitions) {
            return;
        }

        $definitions = array(
            self::DEFINITION_ROLES     => array(),
            self::DEFINITION_RESOURCES => array(),
            self::DEFINITION_ALLOW     => array(),
            self::DEFINITION_DENY      => array()
        );

        $roleRepository = $this->entityManager->getRepository('PhlexibleSecurityBundle:Role');
        $roles = $roleRepository->findAll();

        foreach ($roles as $role) {
            $definitions[self::DEFINITION_ROLES][] = $role->getRole();

            foreach ($role->getResources() as $resource) {
                $op = $resource->getOp();
                if (!$op) {
                    $op = 'allow';
                }
                if (!in_array($op, array(self::DEFINITION_ALLOW, self::DEFINITION_DENY))) {
                    continue;
                }
                $definitions[$op][] = array(
                    $role->getRole(),
                    $resource->getResource()
                );
            }
        }

        $this->definitions = $definitions;
    }
}
