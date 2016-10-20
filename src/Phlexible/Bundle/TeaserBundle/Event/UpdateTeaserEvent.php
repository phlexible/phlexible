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
