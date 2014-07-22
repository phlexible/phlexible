<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Event;

use Phlexible\Bundle\MediaSiteBundle\Driver\Action\ActionInterface;
use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Action file event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AbstractActionFileEvent extends AbstractActionEvent
{
    /**
     * @var FileInterface
     */
    private $file;

    /**
     * @param ActionInterface $action
     * @param FileInterface   $file
     */
    public function __construct(ActionInterface $action, FileInterface $file)
    {
        parent::__construct($action);

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