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
use Phlexible\Bundle\UserBundle\Model\GroupManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * User serializer.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleUserRequestApplier implements UserRequestApplierInterface
{
    /**
     * @var GroupManagerInterface
     */
    private $groupManager;

    public function __construct(GroupManagerInterface $groupManager)
    {
        $this->groupManager = $groupManager;
    }

    public function apply(Request $request, UserInterface $user)
    {
        if ($request->request->get('firstname')) {
            $user->setFirstname($request->request->get('firstname'));
        }
        if ($request->request->get('lastname')) {
            $user->setLastname($request->request->get('lastname'));
        }
        if ($request->request->get('email')) {
            $user->setEmail($request->request->get('email'));
        }
        if ($request->request->get('username')) {
            $user->setUsername($request->request->get('username'));
        }
        if ($request->request->has('comment')) {
            $user->setComment($request->request->get('comment'));
        }

        // password
        if ($request->request->get('password')) {
            $user->setPlainPassword($request->request->get('password'));
        }

        // enabled
        if ($request->request->get('enabled')) {
            $user->setEnabled(true);
        } else {
            $user->setEnabled(false);
        }

        // properties
        $properties = [];
        foreach ($request->request->all() as $key => $value) {
            if (substr($key, 0, 9) === 'property_') {
                $key = substr($key, 9);
                $properties[$key] = $value;
            }
        }
        if (count($properties)) {
            $user->setProperties($properties);
        } else {
            $user->setProperties([]);
        }

        // roles
        $roles = $request->request->get('roles');
        if ($roles) {
            $user->setRoles(explode(',', $roles));
        } else {
            $user->setRoles([]);
        }

        // groups
        $groups = $request->request->get('groups');
        if ($groups) {
            foreach (explode(',', $groups) as $groupId) {
                $group = $this->groupManager->find($groupId);
                $user->addGroup($group);
            }
        } else {
            $groups = $user->getGroups();
            if ($groups) {
                foreach ($groups as $group) {
                    $user->removeGroup($group);
                }
            }
        }
    }
}
