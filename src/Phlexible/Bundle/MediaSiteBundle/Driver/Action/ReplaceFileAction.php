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
 * Replace file action
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ReplaceFileAction extends FileAction
{
    /**
     * @var FileSourceInterface
     */
    private $fileSource;

    /**
     * @var HashCalculatorInterface
     */
    private $hashCalculator;

    /**
     * @param FileInterface           $file
     * @param FileSourceInterface     $fileSource
     * @param HashCalculatorInterface $hashCalculator
     */
    public function __construct(
        FileInterface $file,
        FileSourceInterface $fileSource,
        HashCalculatorInterface $hashCalculator)
    {
        parent::__construct($file);

        $this->fileSource = $fileSource;
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
     * @return HashCalculatorInterface
     */
    public function getHashCalulator()
    {
        return $this->hashCalculator;
    }
}
