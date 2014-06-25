<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Commit;

use Phlexible\Bundle\MediaCacheBundle\Cache\CacheRepository;
use Phlexible\Bundle\MediaCacheBundle\Queue\Queue;
use Phlexible\Bundle\MediaSiteBundle\Site\SiteManager;
use Phlexible\Bundle\MediaTemplateBundle\Model\TemplateManagerInterface;

/**
 * Template change committer
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TemplateChangeCommitter
{
    /**
     * @var TemplateManagerInterface
     */
    private $templateManager;

    /**
     * @var CacheRepository
     */
    private $cacheRepository;

    /**
     * @var SiteManager
     */
    private $siteManager;

    /**
     * @var Queue
     */
    private $queue;

    /**
     * @param TemplateManagerInterface $templateManager
     * @param CacheRepository          $cacheRepository
     * @param SiteManager              $siteManager
     * @param Queue                    $queue
     */
    public function __construct(TemplateManagerInterface $templateManager,
                                CacheRepository $cacheRepository,
                                SiteManager $siteManager,
                                Queue $queue)
    {
        $this->templateManager = $templateManager;
        $this->cacheRepository = $cacheRepository;
        $this->siteManager = $siteManager;
        $this->queue = $queue;
    }

    public function changes()
    {
        $changes = array();

        foreach ($this->templateManager->findAll() as $template) {
            $cacheItems = $this->cacheRepository->findByOutdatedTemplateRevision($template);

            foreach ($cacheItems as $cacheItem) {
                $change = array(
                    'fileId'       => $cacheItem->getFileId(),
                    'fileVersion'  => $cacheItem->getFileVersion(),
                    'fileRevision' => $cacheItem->getTemplateRevision(),
                    'template'     => $template
                );

                $changes[] = $change;
            }
        }

        return $changes;
    }

    /**
     *
     */
    public function commit()
    {
        $changes = $this->changes();

        foreach ($changes as $change) {
            $site = $this->siteManager->getByFileId($change['fileId']);
            $file = $site->findFile($change['fileId'], $change['fileVersion']);
            $this->queue->add($change['template'], $file);
        }
    }
}
