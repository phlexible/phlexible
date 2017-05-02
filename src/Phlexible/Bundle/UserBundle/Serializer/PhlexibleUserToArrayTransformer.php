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
 * User serializer.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleUserToArrayTransformer implements UserToArrayTransformerInterface
{
    public function serialize(UserInterface $user)
    {
        $groups = [];
        foreach ($user->getGroups() as $group) {
            $groups[] = $group->getId();
        }

        $data = [
            'uid' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'comment' => $user->getComment(),
            'enabled' => $user->isEnabled(),
            'roles' => $user->getRoles(),
            'groups' => $groups,
            'createDate' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
            'createUser' => '',
            'modifyDate' => $user->getModifiedAt()->format('Y-m-d H:i:s'),
            'modifyUser' => '',
            'properties' => $user->getProperties(),
        ];

        return $data;
    }
}
