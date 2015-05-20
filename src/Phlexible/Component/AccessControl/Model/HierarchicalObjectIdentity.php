<?php

namespace Phlexible\Component\AccessControl\Model;

use Phlexible\Component\AccessControl\Exception\InvalidDomainObjectException;
use Symfony\Component\Security\Core\Util\ClassUtils;

/**
 * ObjectIdentity implementation.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class HierarchicalObjectIdentity extends ObjectIdentity
{
    private $hierarchicalIdentifiers;

    /**
     * Constructor.
     *
     * @param string $hierarchicalIdentifiers
     * @param string $identifier
     * @param string $type
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($hierarchicalIdentifiers, $identifier, $type)
    {
        if (empty($hierarchicalIdentifiers)) {
            throw new \InvalidArgumentException('$hierarchicalIdentifiers cannot be empty.');
        }
        if (empty($type)) {
            throw new \InvalidArgumentException('$type cannot be empty.');
        }

        $this->hierarchicalIdentifiers = $hierarchicalIdentifiers;

        parent::__construct($identifier, $type);
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
            if ($domainObject instanceof HierarchicalDomainObjectInterface) {
                return new self(
                    $domainObject->getHierarchicalObjectIdentifiers(),
                    $domainObject->getObjectIdentifier(),
                    ClassUtils::getRealClass($domainObject)
                );
            }

            return parent::fromDomainObject($domainObject);
        } catch (\InvalidArgumentException $invalid) {
            throw new InvalidDomainObjectException($invalid->getMessage(), 0, $invalid);
        }

        throw new InvalidDomainObjectException('$domainObject must either implement the HierarchicalDomainObjectInterface, DomainObjectInterface, or have a method named "getId".');
    }

    /**
     * {@inheritdoc}
     */
    public function getHierarchicalIdentifiers()
    {
        return $this->hierarchicalIdentifiers;
    }

    /**
     * Returns a textual representation of this object identity.
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf('ObjectIdentity(%s, %s, %s)', json_encode($this->hierarchicalIdentifiers), $this->getIdentifier(), $this->getType());
    }
}
