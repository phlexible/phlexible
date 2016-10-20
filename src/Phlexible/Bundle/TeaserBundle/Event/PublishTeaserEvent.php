<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TeaserBundle\Event;

use Phlexible\Bundle\TeaserBundle\Entity\Teaser;

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
