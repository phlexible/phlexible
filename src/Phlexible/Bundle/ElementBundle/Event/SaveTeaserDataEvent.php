<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Event;

use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Save teaser data event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SaveTeaserDataEvent extends Event
{
    /**
     * @var Teaser
     */
    private $teaser;

    /**
     * @var string
     */
    private $language;

    /**
     * @var array
     */
    private $data;

    /**
     * @param Teaser $teaser
     * @param string $language
     * @param array  $data
     */
    public function __construct(Teaser $teaser, $language, array $data)
    {
        $this->teaser = $teaser;
        $this->language = $language;
        $this->data = $data;
    }

    /**
     * @return Teaser
     */
    public function getTeaser()
    {
        return $this->teaser;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
