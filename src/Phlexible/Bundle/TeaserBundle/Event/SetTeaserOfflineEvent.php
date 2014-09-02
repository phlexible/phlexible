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
 * Set teaser offline event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SetTeaserOfflineEvent extends TeaserEvent
{
    /**
     * @var string
     */
    private $language;

    /**
     * @param Teaser $teaser
     * @param string $language
     */
    public function __construct(Teaser $teaser, $language)
    {
        parent::__construct($teaser);

        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }
}
