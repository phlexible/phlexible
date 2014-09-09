<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Queue;

use Phlexible\Bundle\MediaSiteBundle\File;
use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;
use Phlexible\Bundle\MediaSiteBundle\Site\SiteManager;
use Phlexible\Bundle\MediaTemplateBundle\Model\TemplateInterface;
use Phlexible\Bundle\MediaTemplateBundle\Model\TemplateManagerInterface;

/**
 * Queue batch
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BatchBuilder
{
    /**
     * @var SiteManager
     */
    private $siteManager;

    /**
     * @var TemplateManagerInterface
     */
    private $templateManager;

    /**
     * @param SiteManager              $siteManager
     * @param TemplateManagerInterface $templateManager
     */
    public function __construct(SiteManager $siteManager, TemplateManagerInterface $templateManager)
    {
        $this->siteManager = $siteManager;
        $this->templateManager = $templateManager;
    }

    /**
     * Create an empty batch.
     *
     * @return Batch
     */
    public function create()
    {
        return new Batch();
    }

    /**
     * Create a new batch with given template and file
     *
     * @param TemplateInterface $template
     * @param FileInterface     $file
     *
     * @return Batch
     */
    public function createForTemplateAndFile(TemplateInterface $template, FileInterface $file)
    {
        $batch = $this->create();

        if ($template->getCache()) {
            $batch
                ->addFile($file)
                ->addTemplate($template);
        }
    }

    /**
     * Create a new batch with all templates
     *
     * @return Batch
     */
    public function createWithAllTemplates()
    {
        $batch = $this->create();

        $this->addAllTemplates($batch);

        return $batch;
    }

    /**
     * Create a new batch with all files
     *
     * @return Batch
     */
    public function createWithAllFiles()
    {
        $batch = $this->create();

        $this->addAllFiles($batch);

        return $batch;
    }

    /**
     * Create a new batch with all templates and all files
     *
     * @return Batch
     */
    public function createWithAllTemplatesAndFiles()
    {
        $batch = $this->create();

        $this
            ->addAllFiles($batch)
            ->addAllTemplates($batch);

        return $batch;
    }

    /**
     * @param Batch $batch
     *
     * @return $this
     */
    private function addAllTemplates(Batch $batch)
    {
        foreach ($this->templateManager->findAll() as $template) {
            if ($template->getCache()) {
                echo $template->getKey().PHP_EOL;
                $batch->addTemplate($template);
            }
        }

        return $this;
    }

    /**
     * @param Batch $batch
     *
     * @return $this
     */
    private function addAllFiles(Batch $batch)
    {
        foreach ($this->siteManager->getAll() as $site) {
            $rii = new \RecursiveIteratorIterator($site->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);

            foreach ($rii as $folder) {
                foreach ($site->findFilesByFolder($folder) as $file) {
                    $batch->addFile($file);
                }
            }
        }

        return $this;
    }
}
