<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Event;

use Phlexible\Bundle\ElementtypeBundle\Elementtype\Elementtype;

/**
 * Elementtype event
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class ElementtypeUsageEvent extends ElementtypeEvent
{
    private $usage = array();

    /**
     * @param Elementtype $elementtype
     */
    public function __construct(Elementtype $elementtype)
    {
        parent::__construct($elementtype);
    }

    /**
     * @param string  $type
     * @param string  $id
     * @param string  $title
     * @param integer $latestVersion
     */
    public function addUsage($type, $id, $title, $latestVersion = null)
    {
        $this->usage[] = array('type' => $type, 'id' => $id, 'title' => $title, 'latest_version' => $latestVersion);
    }

    /**
     * @return array
     */
    public function getUsage()
    {
        return $this->usage;
    }
}