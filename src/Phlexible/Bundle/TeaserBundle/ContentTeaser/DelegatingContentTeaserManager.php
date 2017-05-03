<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TeaserBundle\ContentTeaser;

use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TeaserBundle\Mediator\Mediator;
use Phlexible\Bundle\TeaserBundle\Model\TeaserManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;

// TODO: interface

/**
 * Teaser manager.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DelegatingContentTeaserManager
{
    /**
     * @var TeaserManagerInterface
     */
    private $teaserManager;

    /**
     * @var Mediator
     */
    private $mediator;

    /**
     * @param TeaserManagerInterface $teaserManager
     * @param Mediator               $mediator
     */
    public function __construct(TeaserManagerInterface $teaserManager, Mediator $mediator)
    {
        $this->teaserManager = $teaserManager;
        $this->mediator = $mediator;
    }

    /**
     * @var string
     */
    private $language = 'en';

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @var ContentTeaser[]
     */
    private $contentTeasers = array();

    /**
     * @return ContentTeaser[]
     */
    public function getTeasers()
    {
        return $this->contentTeasers;
    }

    public function xfind($id)
    {
        return $this->createContentTeaserFromTeaser($this->teaserManager->find($id));
    }

    public function xfindBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $teasers = array();
        foreach ($this->teaserManager->findBy($criteria, $orderBy, $limit, $offset) as $teaser) {
            $teasers = $this->createContentTeaserFromTeaser($teaser);
        }

        return $teasers;
    }

    public function xfindOneBy(array $criteria)
    {
        return $this->createContentTeaserFromTeaser($this->teaserManager->findOneBy($criteria));
    }

    public function findForLayoutAreaAndTreeNodePath($layoutarea, array $treeNodePath, $includeLocalHidden = true)
    {
        return $this->createContentTeasersFromTeasers($this->teaserManager->findForLayoutAreaAndTreeNodePath($layoutarea, $treeNodePath, $includeLocalHidden));
    }

    public function findForLayoutAreaAndTreeNode($layoutarea, TreeNodeInterface $treeNode)
    {
        return $this->createContentTeasersFromTeasers($this->teaserManager->findForLayoutAreaAndTreeNode($layoutarea, $treeNode));
    }

    public function isInstance(ContentTeaser $teaser)
    {
        return $this->teaserManager->isInstance($teaser);
    }

    public function isInstanceMaster(ContentTeaser $teaser)
    {
        return $this->teaserManager->isInstanceMaster($teaser);
    }

    public function getInstances(ContentTeaser $teaser)
    {
        return $this->teaserManager->getInstances($teaser);
    }

    public function isPublished(ContentTeaser $teaser, $language)
    {
        return $this->teaserManager->isPublished($teaser, $language);
    }

    public function getPublishedLanguages(ContentTeaser $teaser)
    {
        return $this->teaserManager->getPublishedLanguages($teaser);
    }

    public function getPublishedVersion(ContentTeaser $teaser, $language)
    {
        return $this->teaserManager->getPublishedVersion($teaser, $language);
    }

    public function getPublishedVersions(ContentTeaser $teaser)
    {
        return $this->teaserManager->getPublishedVersions($teaser);
    }

    public function isAsync(Teaser $teaser, $language)
    {
        return $this->teaserManager->isAsync($teaser, $language);
    }

    public function findOnlineByTeaser(ContentTeaser $teaser)
    {
        return $this->teaserManager->findOnlineByTeaser($teaser);
    }

    public function findOneOnlineByTeaserAndLanguage(ContentTeaser $teaser, $language)
    {
        return $this->teaserManager->findOneOnlineByTeaserAndLanguage($teaser, $language);
    }

    /**
     * @param Teaser[] $teasers
     *
     * @return ContentTeaser[]
     */
    private function createContentTeasersFromTeasers(array $teasers)
    {
        $contentTeasers = [];
        foreach ($teasers as $teaser) {
            $contentTeasers[] = $this->createContentTeaserFromTeaser($teaser);
        }

        return $contentTeasers;
    }

    /**
     * @param Teaser $teaser
     *
     * @return ContentTeaser
     */
    private function createContentTeaserFromTeaser(Teaser $teaser)
    {
        if (!isset($this->contentTeasers[$teaser->getId()])) {
            $contentTeaser = new ContentTeaser();
            $contentTeaser
                ->setId($teaser->getId())
                ->setLayoutareaId($teaser->getLayoutareaId())
                ->setTreeId($teaser->getTreeId())
                ->setEid($teaser->getEid())
                ->setTypeId($teaser->getTypeId())
                ->setType($teaser->getType())
                ->setSort($teaser->getSort())
                ->setCache($teaser->getCache())
                ->setAttributes($teaser->getAttributes())
                ->setCreatedAt($teaser->getCreatedAt())
                ->setCreateUserId($teaser->getCreateUserId());

            $contentTeaser->setTitle($this->mediator->getTitle($teaser, 'navigation', $this->language));
            $contentTeaser->setUniqueId($this->mediator->getUniqueId($teaser));

            $this->contentTeasers[$teaser->getId()] = $contentTeaser;
        }

        return $this->contentTeasers[$teaser->getId()];
    }
}
