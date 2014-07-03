<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\EventListener;

use Phlexible\Bundle\GuiBundle\Event\GetConfigEvent;

/**
 * Get config listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GetConfigListener
{
    /**
     * @var int
     */
    private $numFiles;

    /**
     * @var string
     */
    private $view;

    /**
     * @var bool
     */
    private $disableFlash;

    /**
     * @var bool
     */
    private $enableUploadSort;

    /**
     * @var string
     */
    private $deletePolicy;

    /**
     * @param int    $numFiles
     * @param string $view
     * @param bool   $disableFlash
     * @param bool   $enableUploadSort
     * @param string $deletePolicy
     */
    public function __construct($numFiles, $view, $disableFlash, $enableUploadSort, $deletePolicy)
    {
        $this->numFiles = $numFiles;
        $this->view = $view;
        $this->disableFlash = $disableFlash;
        $this->enableUploadSort = $enableUploadSort;
        $this->deletePolicy = $deletePolicy;
    }

    /**
     * @param GetConfigEvent $event
     */
    public function onGetConfig(GetConfigEvent $event)
    {
        $config = $event->getConfig();

        $config
            ->set('mediamanager.files.num_files', (int) $this->numFiles)
            ->set('mediamanager.files.view', $this->view)
            ->set('mediamanager.upload.disable_flash', $this->disableFlash)
            ->set('mediamanager.upload.enable_upload_sort', $this->enableUploadSort)
            ->set('mediamanager.delete_policy', $this->deletePolicy);
    }
}
