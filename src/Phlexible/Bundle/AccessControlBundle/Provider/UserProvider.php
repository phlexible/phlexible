<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\AccessControlBundle\Provider;

use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;

/**
 * User provider
 *
 * @author Marco Fischer <mf@brainbits.net>
 */
class UserProvider implements ProviderInterface
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @param UserManagerInterface $userManager
     */
    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * Return object name
     *
     * @param string $objectType
     * @param string $objectId
     *
     * @return string
     */
    public function getName($objectType, $objectId)
    {
        $user = $this->userManager->find($objectId);

        return $user->getFirstname() . ' ' . $user->getLastname();
    }

    /**
     * Return users
     *
     * @param string $query
     * @param int    $limit
     * @param int    $offset
     *
     * @return array
     */
    public function getAll($query, $limit, $offset)
    {
        // TODO: user query
        $users = $this->userManager->findBy([], ['lastname' => 'ASC'], $limit, $offset);

        $data = [];
        foreach ($users as $user) {
            $name = $user->getFirstname() . ' ' . $user->getLastname();

            $data[] = [
                'type'        => 'user',
                'object_type' => 'uid',
                'object_id'   => $user->getId(),
                'label'       => $name
            ];
        }

        return [
            'total' => $this->userManager->countAll(),
            'data'  => $data,
        ];
    }
}
