<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\EventListener;

use Phlexible\Bundle\GuiBundle\Event\GetConfigEvent;
use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;
use Phlexible\Bundle\TreeBundle\Tree\TreeManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Get config listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GetConfigListener
{
    /**
     * @var SiterootManagerInterface
     */
    private $siterootManager;

    /**
     * @var TreeManager
     */
    private $treeManager;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var bool
     */
    private $publishCommentRequired;

    /**
     * @var bool
     */
    private $publishConfirmRequired;

    /**
     * @var bool
     */
    private $createUseMultilanguage;

    /**
     * @var bool
     */
    private $createRestricted;

    /**
     * @var string
     */
    private $availableLanguages;

    /**
     * @param SiterootManagerInterface      $siterootManager
     * @param TreeManager                   $treeManager
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param bool                          $publishCommentRequired
     * @param bool                          $publishConfirmRequired
     * @param bool                          $createUseMultilanguage
     * @param bool                          $createRestricted
     * @param string                        $availableLanguages
     */
    public function __construct(
        SiterootManagerInterface $siterootManager,
        TreeManager $treeManager,
        AuthorizationCheckerInterface $authorizationChecker,
        $publishCommentRequired,
        $publishConfirmRequired,
        $createUseMultilanguage,
        $createRestricted,
        $availableLanguages
    )
    {
        $this->siterootManager = $siterootManager;
        $this->treeManager = $treeManager;
        $this->authorizationChecker = $authorizationChecker;
        $this->publishCommentRequired = $publishCommentRequired;
        $this->publishConfirmRequired = $publishConfirmRequired;
        $this->createUseMultilanguage = $createUseMultilanguage;
        $this->createRestricted = $createRestricted;
        $this->availableLanguages = $availableLanguages;
    }

    /**
     * @param GetConfigEvent $event
     */
    public function onGetConfig(GetConfigEvent $event)
    {
        $config = $event->getConfig();

        $config->set('elements.publish.comment_required', (bool) $this->publishCommentRequired);
        $config->set('elements.publish.confirm_required', (bool) $this->publishConfirmRequired);
        $config->set('elements.create.use_multilanguage', (bool) $this->createUseMultilanguage);
        $config->set('elements.create.restricted', (bool) $this->createRestricted);

        $siteroots = $this->siterootManager->findAll();
        $allLanguages = explode(',', $this->availableLanguages);

        $siterootLanguages = [];
        $siterootConfig = [];

        foreach ($siteroots as $siteroot) {
            $siterootId = $siteroot->getId();

            if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
                $siterootLanguages[$siterootId] = $allLanguages;
            } else {
                $siterootLanguages[$siterootId] = [];

                foreach ($allLanguages as $language) {
                    $tree = $this->treeManager->getBySiterootId($siterootId);
                    $root = $tree->getRoot();

                    if (!$this->authorizationChecker->isGranted(['right' => 'VIEW', 'language' => $language], $root)) {
                        continue;
                    }

                    $siterootLanguages[$siterootId][] = $language;
                }
            }

            if (count($siterootLanguages[$siterootId])) {
                $siterootConfig[$siterootId] = [
                    'id' => $siteroot->getId(),
                    'title' => $siteroot->getTitle(),
                ];
            }
        }

        $config->set('user.siteroot.languages', $siterootLanguages);

        $config->set('siteroot.config', $siterootConfig);
    }
}
