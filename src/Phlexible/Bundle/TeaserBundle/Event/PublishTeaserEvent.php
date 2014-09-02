<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Event;

use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Symfony\Component\EventDispatcher\Event;

/**
 * Publish teaser event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PublishTeaserEvent extends TeaserEvent
{
    /**
     * @var string
     */
    private $language;

    /**
     * @var int
     */
    private $version;

    /**
     * @param Teaser $teaser
     * @param string $language
     * @param int    $version
     */
    public function __construct(Teaser $teaser, $language, $version)
    {
        parent::__construct($teaser);

        $this->language = $language;
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Return version
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }
}
