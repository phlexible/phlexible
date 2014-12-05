<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Volume\Event;

use Phlexible\Component\Volume\Model\FileInterface;

/**
 * Rename file event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RenameFileEvent extends FileEvent
{
    /**
     * @var string
     */
    private $oldName;

    /**
     * @param FileInterface $file
     * @param string        $oldName
     */
    public function __construct(FileInterface $file, $oldName)
    {
        parent::__construct($file);

        $this->oldName = $oldName;
    }

    /**
     * @return string
     */
    public function getOldName()
    {
        return $this->oldName;
    }
}
