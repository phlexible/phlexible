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
 * Set teaser offline event.
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
