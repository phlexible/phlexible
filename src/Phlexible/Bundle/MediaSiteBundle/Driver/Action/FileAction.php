<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Driver\Action;

use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;

/**
 * File action
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class FileAction extends Action
{
    /**
     * @var FileInterface
     */
    private $file;

    /**
     * @param FileInterface $file
     * @param \DateTime     $date
     * @param string        $userId
     */
    public function __construct(FileInterface $file, \DateTime $date, $userId)
    {
        parent::__construct($date, $userId);

        $this->file = $file;
    }

    /**
     * @return FileInterface
     */
    public function getFile()
    {
        return $this->file;
    }
}
