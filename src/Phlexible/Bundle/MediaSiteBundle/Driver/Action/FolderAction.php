<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Driver\Action;

use Phlexible\Bundle\MediaSiteBundle\Model\FolderInterface;

/**
 * Folder action
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class FolderAction extends Action
{
    /**
     * @var FolderInterface
     */
    private $folder;

    /**
     * @param FolderInterface $folder
     * @param \DateTime       $date
     * @param string          $userId
     */
    public function __construct(FolderInterface $folder, \DateTime $date, $userId)
    {
        parent::__construct($date, $userId);

        $this->folder = $folder;
    }

    /**
     * @return FolderInterface
     */
    public function getFolder()
    {
        return $this->folder;
    }
}
