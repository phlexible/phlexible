<?php

namespace Phlexible\Component\AccessControl\Model;

/**
 * This interface provides an additional level of indirection, so that
 * we can work with abstracted versions of security objects and do
 * not have to save the entire objects.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface SecurityIdentityInterface
{
    /**
     * Returns the identifier.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Returns the type.
     *
     * @return string
     */
    public function getType();
}
