<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Search;

use Phlexible\Bundle\SearchBundle\Search\SearchResult;
use Phlexible\Bundle\SearchBundle\SearchProvider\SearchProviderInterface;
use Phlexible\Bundle\UserBundle\Entity\User;
use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;

/**
 * User search
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UserSearch implements SearchProviderInterface
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
     * {@inheritdoc}
     */
    public function getResource()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchKey()
    {
        return 'u';
    }

    /**
     * {@inheritdoc}
     */
    public function search($query)
    {
        $users = $this->userManager->search($query);

        $createUser = new User();
        $createUser->setUsername('(unknown)');

        $results = array();
        foreach ($users as $user) {
            $results[] = new SearchResult(
                $user->getId(),
                $user->getDisplayName(),
                $createUser->getDisplayName(),
                $user->getCreatedAt()->format('U'),
                '/bundles/phlexibleuser/icons/user.png',
                'Users Search',
                array(
                    'xtype'      => 'Phlexible.users.menuhandle.UsersHandle',
                    'parameters' => array(
                        'userId' => $user->getId(),
                        'query'  => $user->getUsername()
                    ),
                )
            );
        }

        return $results;
    }
}
