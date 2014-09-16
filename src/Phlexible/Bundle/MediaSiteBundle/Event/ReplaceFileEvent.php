<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Event;

use Phlexible\Bundle\MediaSiteBundle\FileSource\FileSourceInterface;
use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;

/**
 * Replace file event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ReplaceFileEvent extends FileEvent
{
    /**
     * @var FileSourceInterface
     */
    private $fileSource;

    /**
     * @param FileInterface       $file
     * @param FileSourceInterface $fileSource
     */
    public function __construct(FileInterface $file, FileSourceInterface $fileSource)
    {
        parent::__construct($file);

        $this->fileSource = $fileSource;
    }

    /**
     * @return FileSourceInterface
     */
    public function getFileSource()
    {
        return $this->fileSource;
    }
}