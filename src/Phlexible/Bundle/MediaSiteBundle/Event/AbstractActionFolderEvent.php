<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Event;

use Phlexible\Bundle\MediaSiteBundle\Driver\Action\ActionInterface;
use Phlexible\Bundle\MediaSiteBundle\Model\FolderInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Action folderevent
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AbstractActionFolderEvent extends AbstractActionEvent
{
    /**
     * @var FolderInterface
     */
    private $folder;

    /**
     * @param ActionInterface $action
     * @param FolderInterface $folder
     */
    public function __construct(ActionInterface $action, FolderInterface $folder)
    {
        parent::__construct($action);

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