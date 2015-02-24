<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Event;

use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

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
     * @var Request
     */
    private $request;

    /**
     * @param Teaser  $teaser
     * @param string  $language
     * @param Request $request
     */
    public function __construct(Teaser $teaser, $language, Request $request)
    {
        $this->teaser = $teaser;
        $this->language = $language;
        $this->request = $request;
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
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
