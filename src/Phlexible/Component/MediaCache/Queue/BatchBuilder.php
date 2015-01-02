<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaCache\Queue;

use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Phlexible\Component\MediaTemplate\Model\TemplateManagerInterface;
use Phlexible\Component\Volume\VolumeManager;

/**
 * Queue batch
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BatchBuilder
{
    /**
     * @var VolumeManager
     */
    private $volumeManager;

    /**
     * @var TemplateManagerInterface
     */
    private $templateManager;

    /**
     * @param VolumeManager            $volumeManager
     * @param TemplateManagerInterface $templateManager
     */
    public function __construct(VolumeManager $volumeManager, TemplateManagerInterface $templateManager)
    {
        $this->volumeManager = $volumeManager;
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
     * @param TemplateInterface     $template
     * @param ExtendedFileInterface $file
     *
     * @return Batch
     */
    public function createForTemplateAndFile(TemplateInterface $template, ExtendedFileInterface $file)
    {
        $batch = $this->create();

        if ($template->getCache()) {
            $batch
                ->addFile($file)
                ->addTemplate($template);
        }

        return $batch;
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
        foreach ($this->volumeManager->getAll() as $volume) {
            $rii = new \RecursiveIteratorIterator($volume->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);

            foreach ($rii as $folder) {
                foreach ($volume->findFilesByFolder($folder) as $file) {
                    $batch->addFile($file);
                }
            }
        }

        return $this;
    }
}
