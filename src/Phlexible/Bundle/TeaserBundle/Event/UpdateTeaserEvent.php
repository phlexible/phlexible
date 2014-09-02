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
 * Teaser event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UpdateTeaserEvent extends TeaserEvent
{
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
        parent::__construct($teaser);

        $this->language = $language;
        $this->data = $data;
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
