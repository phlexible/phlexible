<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\UserBundle\Serializer;

use FOS\UserBundle\Model\UserInterface;

/**
 * Users to array transformer.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface UserToArrayTransformerInterface
{
    public function serialize(UserInterface $user);
}
