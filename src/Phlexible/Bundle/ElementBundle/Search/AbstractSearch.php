<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Search;

use Doctrine\DBAL\Connection;
use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Icon\IconResolver;
use Phlexible\Bundle\SearchBundle\Search\SearchResult;
use Phlexible\Bundle\SearchBundle\SearchProvider\SearchProviderInterface;
use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;
use Phlexible\Bundle\TreeBundle\Tree\TreeManager;
use Phlexible\Bundle\UserBundle\Entity\User;
use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Abstract element search
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class AbstractSearch implements SearchProviderInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var TreeManager
     */
    private $treeManager;

    /**
     * @var SiterootManagerInterface
     */
    private $siterootManager;

    /**
     * @var IconResolver
     */
    private $iconResolver;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var string
     */
    private $defaultLanguage;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @param Connection                    $connection
     * @param ElementService                $elementService
     * @param TreeManager                   $treeManager
     * @param SiterootManagerInterface      $siterootManager
     * @param IconResolver                  $iconResolver
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param UserManagerInterface          $userManager
     * @param string                        $defaultLanguage
     */
    public function __construct(
        Connection $connection,
        ElementService $elementService,
        TreeManager $treeManager,
        SiterootManagerInterface $siterootManager,
        IconResolver $iconResolver,
        AuthorizationCheckerInterface $authorizationChecker,
        UserManagerInterface $userManager,
        $defaultLanguage)
    {
        $this->connection = $connection;
        $this->elementService = $elementService;
        $this->treeManager = $treeManager;
        $this->siterootManager = $siterootManager;
        $this->iconResolver = $iconResolver;
        $this->authorizationChecker = $authorizationChecker;
        $this->userManager = $userManager;
        $this->defaultLanguage = $defaultLanguage;
    }

    /**
     * {@inheritdoc}
     */
    public function getRole()
    {
        return 'ROLE_ELEMENTS';
    }

    /**
     * @return Connection
     */
    protected function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return string
     */
    protected function getDefaultLanguage()
    {
        return $this->defaultLanguage;
    }

    /**
     * Perform search
     *
     * @param array  $rows
     * @param string $title
     * @param string $language
     *
     * @return array
     */
    protected function doSearch(array $rows, $title, $language = null)
    {
        if ($language === null) {
            $language = $this->defaultLanguage;
        }

        $results = [];
        foreach ($rows as $row) {
            $node = $this->treeManager->getByNodeId($row['id'])->get($row['id']);

            if (!$this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') && !$this->authorizationChecker->isGranted('VIEW', $node)) {
                continue;
            }

            $element = $this->elementService->findElement($node->getTypeId());
            $elementVersion = $this->elementService->findLatestElementVersion($element);
            $siteroot = $this->siterootManager->find($node->getTree()->getSiterootId());

            $handlerData = array(
                'handler' => 'element',
                'parameters' => array(
                    'id' => $node->getId(),
                    'siteroot_id' => $node->getTree()->getSiterootId(),
                    'title' => $siteroot->getTitle($language),
                    'start_tid_path' => '/' . implode('/', $node->getTree()->getIdPath($node)),
                )
            );

            try {
                $createUser = $this->userManager->find($elementVersion->getCreateUserId());
            } catch (\Exception $e) {
                $createUser = $this->userManager->getSystemUser();
            }

            $icon = $this->iconResolver->resolveTreeNode($node, $language);

            $results[] = new SearchResult(
                $node->getId(),
                $siteroot->getTitle($language) . ' :: ' . $elementVersion->getBackendTitle($language) . ' (' . $language . ', ' . $node->getId() . ')',
                $createUser->getDisplayName(),
                $elementVersion->getCreatedAt(),
                $icon,
                $title,
                $handlerData
            );
        }

        return $results;
    }
}
