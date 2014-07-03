<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Driver\Action;

use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;
use Phlexible\Bundle\MediaSiteBundle\FileSource\FileSourceInterface;
use Phlexible\Bundle\MediaSiteBundle\Folder\FolderInterface;
use Phlexible\Bundle\MediaSiteBundle\HashCalculator\HashCalculatorInterface;

/**
 * Create file action
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CreateFileAction extends FileAction
{
    /**
     * @var FileSourceInterface
     */
    private $fileSource;

    /**
     * @var FolderInterface
     */
    private $targetFolder;

    /**
     * @var HashCalculatorInterface
     */
    private $hashCalculator;

    /**
     * @param FileInterface           $file
     * @param FileSourceInterface     $fileSource
     * @param FolderInterface         $targetFolder
     * @param HashCalculatorInterface $hashCalculator
     */
    public function __construct(
        FileInterface $file,
        FileSourceInterface $fileSource,
        FolderInterface $targetFolder,
        HashCalculatorInterface $hashCalculator)
    {
        parent::__construct($file);

        $this->fileSource = $fileSource;
        $this->targetFolder = $targetFolder;
        $this->hashCalculator = $hashCalculator;
    }

    /**
     * @return FileSourceInterface
     */
    public function getFileSource()
    {
        return $this->fileSource;
    }

    /**
     * @return FolderInterface
     */
    public function getTargetFolder()
    {
        return $this->targetFolder;
    }

    /**
     * @return HashCalculatorInterface
     */
    public function getHashCalulator()
    {
        return $this->hashCalculator;
    }
}
