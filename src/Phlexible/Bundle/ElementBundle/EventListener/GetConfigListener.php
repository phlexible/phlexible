<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
     * @var array
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
        $this->availableLanguages = explode(',', $availableLanguages);
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

        $siterootLanguages = [];
        $siterootConfig = [];

        foreach ($siteroots as $siteroot) {
            $siterootId = $siteroot->getId();

            if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
                $siterootLanguages[$siterootId] = $this->availableLanguages;
            } else {
                $siterootLanguages[$siterootId] = [];

                $tree = $this->treeManager->getBySiterootId($siterootId);
                $root = $tree->getRoot();

                foreach ($this->availableLanguages as $language) {
                    if (!$this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') && !$this->authorizationChecker->isGranted(['permission' => 'VIEW', 'language' => $language], $root)) {
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
            } else {
                unset($siterootLanguages[$siterootId]);
            }
        }

        $config->set('user.siteroot.languages', $siterootLanguages);

        $config->set('siteroot.config', $siterootConfig);
    }
}
