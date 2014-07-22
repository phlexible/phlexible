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
 * Rename folder action
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RenameFolderAction extends FolderAction
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param FolderInterface $file
     * @param string          $name
     * @param \DateTime       $date
     * @param string          $userId
     */
    public function __construct(FolderInterface $file, $name, \DateTime $date, $userId)
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
