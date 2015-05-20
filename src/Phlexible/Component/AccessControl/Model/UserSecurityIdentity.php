<?php

namespace Phlexible\Component\AccessControl\Model;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Util\ClassUtils;

/**
 * A SecurityIdentity implementation used for actual users.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
final class UserSecurityIdentity implements SecurityIdentityInterface
{
    private $id;
    private $type;

    /**
     * Constructor.
     *
     * @param string $id   the username representation
     * @param string $type the user's fully qualified class name
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($id, $type)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id must not be empty.');
        }
        if (empty($type)) {
            throw new \InvalidArgumentException('$type must not be empty.');
        }

        $this->id = $id;
        $this->type = $type;
    }

    /**
     * Creates a user security identity from a UserInterface.
     *
     * @param UserInterface $user
     *
     * @return UserSecurityIdentity
     */
    public static function fromAccount(UserInterface $user)
    {
        return new self($user->getId(), ClassUtils::getRealClass($user));
    }

    /**
     * Creates a user security identity from a TokenInterface.
     *
     * @param TokenInterface $token
     *
     * @return UserSecurityIdentity
     */
    public static function fromToken(TokenInterface $token)
    {
        $user = $token->getUser();

        if ($user instanceof UserInterface) {
            return self::fromAccount($user);
        }

        return new self((string) $user, is_object($user) ? ClassUtils::getRealClass($user) : ClassUtils::getRealClass($token));
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * A textual representation of this security identity.
     *
     * This is not used for equality comparison, but only for debugging.
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf('UserSecurityIdentity(%s, %s)', $this->username, $this->class);
    }
}
