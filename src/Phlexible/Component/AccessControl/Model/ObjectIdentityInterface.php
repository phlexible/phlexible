<?php

namespace Phlexible\Component\AccessControl\Model;

/**
 * Represents the identity of an individual domain object instance.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface ObjectIdentityInterface
{
    /**
     * Obtains a unique identifier for this object. The identifier must not be
     * re-used for other objects with the same type.
     *
     * @return string cannot return null
     */
    public function getIdentifier();

    /**
     * Returns a type for the domain object. Typically, this is the PHP class name.
     *
     * @return string cannot return null
     */
    public function getType();
}
