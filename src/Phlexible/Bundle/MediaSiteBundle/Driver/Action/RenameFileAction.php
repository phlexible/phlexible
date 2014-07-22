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
     * @param FileInterface $file
     * @param string        $name
     * @param \DateTime     $date
     * @param string        $userId
     */
    public function __construct(FileInterface $file, $name, \DateTime $date, $userId)
    {
        parent::__construct($file, $date, $userId);

        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
