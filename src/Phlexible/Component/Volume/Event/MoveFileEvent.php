<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Volume\Event;

use Phlexible\Component\Volume\Model\FileInterface;
use Phlexible\Component\Volume\Model\FolderInterface;

/**
 * Move file event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MoveFileEvent extends FileEvent
{
    /**
     * @var FolderInterface
     */
    private $targetFolder;

    /**
     * @param FileInterface   $file
     * @param FolderInterface $targetFolder
     */
    public function __construct(FileInterface $file, FolderInterface $targetFolder)
    {
        parent::__construct($file);

        $this->targetFolder = $targetFolder;
    }

    /**
     * @return FolderInterface
     */
    public function getTargetFolder()
    {
        return $this->targetFolder;
    }
}
