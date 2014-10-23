<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Controller;

use FOS\UserBundle\Model\UserInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Phlexible\Bundle\SecurityBundle\Acl\Acl;
use Phlexible\Bundle\UserBundle\Entity\User;
use Phlexible\Bundle\UserBundle\Password\PasswordGenerator;
use Phlexible\Bundle\UserBundle\UsersMessage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Users controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/users/users")
 * @Security("is_granted('ROLE_USERS')")
 */
class UsersController extends Controller
{
    /**
     * List users
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("", name="users_users_list")
     * @Method({"GET", "POST"})
     * @ApiDoc(
     *   description="Returns a list of users",
     *   filters={
     *      {"name"="start", "dataType"="integer", "description"="Start index", "default"=0},
     *      {"name"="limit", "dataType"="integer", "description"="Limit results", "default"=20},
     *      {"name"="sort", "dataType"="string", "description"="Sort field", "default"="username"},
     *      {"name"="dir", "dataType"="string", "description"="Sort direction", "default"="ASC"}
     *   }
     * )
     */
    public function listAction(Request $request)
    {
        $start = $request->get('start');
        $limit = $request->get('limit', 20);
        $sort = $request->get('sort', 'username');
        $dir = $request->get('dir', 'ASC');
        $search = $request->get('search', null);

        if ($search) {
            $search = json_decode($search, true);
        }

        $userManager = $this->get('phlexible_user.user_manager');
        $securityContext = $this->get('security.context');

        $allUsers = $userManager->findAll();
        $systemUserUid = $userManager->getSystemUserId();

        $users = [];
        $sortField = [];

        foreach ($allUsers as $user) {
            /* @var $user UserInterface */

            //if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {
            //    continue;
            //}

            if ($user->getId() === $systemUserUid && !$securityContext->isGranted('ROLE_SUPER_ADMIN')
            ) {
                continue;
            }

            if ($search !== null) {
                foreach ($search as $key => $value) {
                    if (!$value) {
                        continue;
                    }

                    if ($key == 'key' &&
                        stripos($user->getUsername(), $value) === false &&
                        stripos($user->getEmail(), $value) === false &&
                        stripos($user->getFirstname(), $value) === false &&
                        stripos($user->getLastname(), $value) === false
                    ) {
                        continue 2;
                    }

                    //                    if ($key == 'account_active')
                    //                    {
                    //
                    //                    }

                    if ($key == 'account_expired') {
                        if (!$user->getExpiresAt() || $user->getExpiresAt()->format('U') > time()) {
                            continue 2;
                        }
                    }

                    if ($key == 'account_has_expire_date') {
                        if (!$user->getExpiresAt()) {
                            continue 2;
                        }
                    }

                    if (substr($key, 0, 5) == 'role_') {
                        $role = strtoupper(substr($key, 5));
                        if (!in_array($role, $user->getRoles())) {
                            continue 2;
                        }
                    }

                    if (substr($key, 0, 6) == 'group_') {
                        $groupId = substr($key, 6);
                        $group = $this->get('phlexible_user.group_manager')->find($groupId);
                        if (!$user->hasGroup($group)) {
                            continue 2;
                        }
                    }
                }
            }

            $groups = [];
            foreach ($user->getGroups() as $group) {
                $groups[] = $group->getId();
            }

            $dummy = [
                'uid'        => $user->getId(),
                'username'   => $user->getUsername(),
                'email'      => $user->getEmail(),
                'firstname'  => $user->getFirstname(),
                'lastname'   => $user->getLastname(),
                'comment'    => $user->getComment(),
                'expireDate' => $user->getExpiresAt() ? $user->getExpiresAt()->format('Y-m-d H:i:s') : null,
                'roles'      => $user->getRoles(),
                'groups'     => $groups,
                'createDate' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
                'createUser' => '',
                'modifyDate' => $user->getModifiedAt()->format('Y-m-d H:i:s'),
                'modifyUser' => '',
                'properties' => $user->getProperties(),
            ];

            $users[] = $dummy;

            $sortField[] = strtolower($dummy[$sort]);
        }

        if (strtoupper($dir) == 'ASC') {
            array_multisort($sortField, SORT_ASC, SORT_STRING, $users);
        } else {
            array_multisort($sortField, SORT_DESC, SORT_STRING, $users);
        }

        $count = count($users);
        $users = array_slice($users, $start, $limit);

        return new JsonResponse(
            [
                'users' => $users,
                'count' => $count
            ]
        );
    }

    /**
     * Create user
     *
     * @param Request $request
     *
     * @throws \Exception
     * @return ResultResponse
     * @Route("/create", name="users_users_create")
     * @Method("POST")
     * @ApiDoc(
     *   description="Create user",
     *   requirements={
     *     {"name"="username", "dataType"="string", "required"=true, "description"="Username"},
     *     {"name"="email", "dataType"="string", "required"=true, "description"="Email"},
     *     {"name"="password", "dataType"="string", "required"=false, "description"="password"},
     *     {"name"="firstname", "dataType"="string", "required"=true, "description"="Firstname"},
     *     {"name"="lastname", "dataType"="string", "required"=true, "description"="Lastname"},
     *     {"name"="roles", "dataType"="array", "required"=false, "description"="Roles"},
     *     {"name"="groups", "dataType"="array", "required"=false, "description"="Groups"},
     *     {"name"="property_*", "dataType"="string", "required"=false, "description"="Property"}
     *   }
     * )
     */
    public function createAction(Request $request)
    {
        $userManager = $this->get('phlexible_user.user_manager');

        if ($request->get('username') && $userManager->checkUsername($request->get('username'))) {
            throw new \Exception('Username "' . $request->get('username') . '" already exists.');
        }
        if ($request->get('email') && $userManager->checkEmail($request->get('email'))) {
            throw new \Exception('Email "' . $request->get('email') . '" already exists.');
        }

        $user = $userManager->create();

        $this->requestToUser($request, $user);

        $user
            ->setCreatedAt(new \DateTime())
            ->setModifiedAt(new \DateTime());

        $optin = (bool) $request->request->get('optin', false);
        if ($optin) {
            $user->setPasswordToken(Uuid::generate());

            $mailer = $this->get('phlexible_user.mailer');
            $mailer->sendNewAccountEmailMessage($user);
        }

        $userManager->updateUser($user);

        $this->get('phlexible_message.message_poster')
            ->post(UsersMessage::create('User "' . $user->getUsername() . '" created.'));

        return new ResultResponse(true, "User {$user->getUsername()} created.");
    }

    /**
     * Update user
     *
     * @param Request $request
     * @param string  $userId
     *
     * @throws \Exception
     * @return ResultResponse
     * @Route("/{userId}", name="users_users_update")
     * @Method("PUT")
     * @ApiDoc(
     *   description="Update user",
     *   requirements={
     *     {"name"="username", "dataType"="string", "required"=true, "description"="Username"},
     *     {"name"="email", "dataType"="string", "required"=true, "description"="Email"},
     *     {"name"="password", "dataType"="string", "required"=false, "description"="password"},
     *     {"name"="firstname", "dataType"="string", "required"=true, "description"="Firstname"},
     *     {"name"="lastname", "dataType"="string", "required"=true, "description"="Lastname"},
     *     {"name"="roles", "dataType"="array", "required"=false, "description"="Roles"},
     *     {"name"="groups", "dataType"="array", "required"=false, "description"="Groups"},
     *     {"name"="property_*", "dataType"="string", "required"=false, "description"="Property"}
     *   }
     * )
     */
    public function updateAction(Request $request, $userId)
    {
        $userManager = $this->get('phlexible_user.user_manager');

        $user = $userManager->find($userId);
        /* @var $user User */

        if ($request->get('username') && $request->get('username') !== $user->getUsername()
                && $userManager->checkUsername($request->get('username'))) {
            throw new \Exception('Username "' . $request->get('username') . '" already exists.');
        }
        if ($request->get('email') && $request->get('email') !== $user->getEmail()
                && $userManager->checkEmail($request->get('email'))) {
            throw new \Exception('Email "' . $request->get('email') . '" already exists.');
        }

        $this->requestToUser($request, $user);

        $user
            ->setModifiedAt(new \DateTime());

        $optin = (bool) $request->request->get('optin', false);
        if ($optin) {
            $user->setPasswordToken(Uuid::generate());

            $mailer = $this->get('phlexible_user.mailer');
            $mailer->sendNewPasswordEmailMessage($user);
        }

        $userManager->updateUser($user);

        $this->get('phlexible_message.message_poster')
            ->post(UsersMessage::create('User "' . $user->getUsername() . '" updated.'));

        return new ResultResponse(true, "User {$user->getUsername()} updated.");
    }

    /**
     * @param Request $request
     * @param User    $user
     */
    private function requestToUser(Request $request, User $user)
    {
        if ($request->request->get('firstname')) {
            $user->setFirstname($request->get('firstname'));
        }
        if ($request->request->get('lastname')) {
            $user->setLastname($request->get('lastname'));
        }
        if ($request->request->get('email')) {
            $user->setEmail($request->get('email'));
        }
        if ($request->request->get('username')) {
            $user->setUsername($request->get('username'));
        }
        if ($request->request->get('comment')) {
            $user->setComment($request->get('comment'));
        }

        // password
        if ($request->request->get('password')) {
            $user->setPlainPassword($request->request->get('password'));
        }

        // expires
        if ($request->request->get('expires')) {
            $user->setExpiresAt(new \DateTime($request->get('expires')));
        } else {
            $user->setExpiresAt(null);
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
            $groupManager = $this->get('phlexible_user.group_manager');
            foreach (explode(',', $groups) as $groupId) {
                $group = $groupManager->find($groupId);
                $user->addGroup($group);
            }
        }
    }

    /**
     * Delete users
     *
     * @param Request $request
     * @param string  $userId
     *
     * @return ResultResponse
     * @Route("/{userId}", name="users_users_delete")
     * @Method("DELETE")
     * @ApiDoc(
     *   description="Delete user"
     * )
     */
    public function deleteAction(Request $request, $userId)
    {
        $successorUserId = $request->request->get('successor');

        $userManager = $this->get('phlexible_user.user_manager');

        $successorUser = $userManager->find($successorUserId);
        $user = $userManager->find($userId);

        $userManager->deleteUser($user, $successorUser);

        $this->get('phlexible_message.message_poster')
            ->post(UsersMessage::create('User "' . $user->getUsername() . '" deleted.'));

        return new ResultResponse(true);
    }

    /**
     * Return filter values
     *
     * @return JsonResponse
     * @Route("/filtervalues", name="users_users_filtervalues")
     * @Method("GET")
     * @ApiDoc(
     *   description="List filter values"
     * )
     */
    public function filtervaluesAction()
    {
        $securityContext = $this->get('security.context');
        $groupManager = $this->get('phlexible_user.group_manager');

        $allGroups = $groupManager->findAll();
        $everyoneGroupId = $groupManager->getEveryoneGroupId();

        $groups = [];
        foreach ($allGroups as $group) {
            if ($group->getId() == $everyoneGroupId) {
                continue;
            }

            $groups[] = [
                'id'    => $group->getId(),
                'title' => $group->getName()
            ];
        }

        $roles = [];
        foreach ($this->container->getParameter('security.role_hierarchy.roles') as $role => $subRoles) {
            if (!$securityContext->isGranted($role)) {
                continue;
            }

            $roles[] = ['id' => $role, 'title' => ucfirst(str_replace('_', ' ', $role))];
        }

        $data = [
            'groups' => $groups,
            'roles'  => $roles,
        ];

        return new JsonResponse($data);
    }

    /**
     * Create password
     *
     * @return JsonResponse
     * @Route("/password", name="users_password")
     * @Method("GET")
     * @ApiDoc(
     *   description="Create password"
     * )
     */
    public function passwordAction()
    {
        $minLength = $this->container->getParameter('phlexible_user.password.min_length');

        $generator = new PasswordGenerator();
        $password = $generator->create($minLength, PasswordGenerator::TYPE_UNPRONOUNCABLE);

        return new JsonResponse(
            [
                'password' => $password,
                'success'  => true
            ]
        );
    }
}
