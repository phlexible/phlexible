<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Event;

use Phlexible\Bundle\ElementBundle\Entity\Element;
use Symfony\Component\EventDispatcher\Event;

/**
 * Save element event.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SaveElementEvent extends Event
{
    /**
     * @var Element
     */
    private $element;

    /**
     * @var string
     */
    private $language;

    /**
     * @var int
     */
    private $oldVersion;

    /**
     * @param Element $element
     * @param string  $language
     * @param int     $oldVersion
     */
    public function __construct(Element $element, $language, $oldVersion)
    {
        $this->element = $element;
        $this->language = $language;
        $this->oldVersion = $oldVersion;
    }

    /**
     * @return Element
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return int
     */
    public function getOldVersion()
    {
        return $this->oldVersion;
    }
}
