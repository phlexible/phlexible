<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Driver\Action;

use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;

/**
 * Rename file action
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RenameFileAction extends FileAction
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var string
     */
    private $userId;

    /**
     * @param FileInterface $file
     * @param string        $name
     * @param \DateTime     $date
     * @param string        $userId
     */
    public function __construct(FileInterface $file, $name, \DateTime $date, $userId)
    {
        parent::__construct($file);

        $this->name = $name;
        $this->date = $date;
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    public function getUserId()
    {
        return $this->userId;
    }
}
