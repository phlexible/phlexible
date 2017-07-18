<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\AccessControl\Domain;

use Phlexible\Component\AccessControl\Exception\InvalidDomainObjectException;
use Phlexible\Component\AccessControl\Model\DomainObjectInterface;
use Phlexible\Component\AccessControl\Model\ObjectIdentityInterface;
use Symfony\Component\Security\Core\Util\ClassUtils;

/**
 * Object identity.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ObjectIdentity implements ObjectIdentityInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @var string
     */
    private $type;

    /**
     * Constructor.
     *
     * @param string $identifier
     * @param string $type
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($identifier, $type)
    {
        if (empty($identifier)) {
            throw new \InvalidArgumentException('$identifier cannot be empty.');
        }
        if (empty($type)) {
            throw new \InvalidArgumentException('$type cannot be empty.');
        }

        $this->identifier = (string) $identifier;
        $this->type = (string) $type;
    }

    /**
     * Constructs an ObjectIdentity for the given domain object.
     *
     * @param object $domainObject
     *
     * @throws InvalidDomainObjectException
     *
     * @return ObjectIdentity
     */
    public static function fromDomainObject($domainObject)
    {
        if (!is_object($domainObject)) {
            throw new InvalidDomainObjectException('$domainObject must be an object.');
        }

        try {
            if ($domainObject instanceof DomainObjectInterface) {
                return new self($domainObject->getObjectIdentifier(), ClassUtils::getRealClass($domainObject));
            } elseif (method_exists($domainObject, 'getId')) {
                return new self($domainObject->getId(), ClassUtils::getRealClass($domainObject));
            }
        } catch (\InvalidArgumentException $invalid) {
            throw new InvalidDomainObjectException($invalid->getMessage(), 0, $invalid);
        }

        throw new InvalidDomainObjectException('$domainObject must either implement the DomainObjectInterface, or have a method named "getId".');
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns a textual representation of this object identity.
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf('ObjectIdentity(%s, %s)', $this->identifier, $this->type);
    }
}
