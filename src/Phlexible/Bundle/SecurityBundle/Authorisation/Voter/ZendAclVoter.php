<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SecurityBundle\Authorisation\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Zend ACL Voter
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ZendAclVoter implements VoterInterface
{
    /**
     * @var \Zend_Acl
     */
    private $acl;

    /**
     * @param \Zend_Acl $acl
     */
    public function __construct(\Zend_Acl $acl)
    {
        $this->acl = $acl;
    }

    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        $resource = current($attributes);

        if (!is_object($token->getUser())) {
            return self::ACCESS_DENIED;
        }

        if ($token->getUser()->hasRole('superadmin') || $token->getUser()->hasRole('developer')) {
            return self::ACCESS_GRANTED;
        }

        if (!$this->acl->has($resource)) {
            return self::ACCESS_ABSTAIN;
        }

        foreach ($token->getUser()->getRoles() as $role) {
            if ($this->acl->isAllowed($role, $resource)) {
                return self::ACCESS_GRANTED;
            }
        }

        return self::ACCESS_DENIED;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsAttribute($attribute)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return true;
    }
}