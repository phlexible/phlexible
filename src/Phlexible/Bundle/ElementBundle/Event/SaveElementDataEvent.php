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

use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Symfony\Component\EventDispatcher\Event;

/**
 * Save element data event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SaveElementDataEvent extends Event
{
    /**
     * @var ElementVersion
     */
    private $elementVersion;

    /**
     * @var array
     */
    private $values;

    /**
     * @var string
     */
    private $language;

    /**
     * @var int
     */
    private $oldVersion;

    /**
     * @param ElementVersion $elementVersion
     * @param array          $values
     * @param string         $language
     * @param int            $oldVersion
     */
    public function __construct(ElementVersion $elementVersion, array $values, $language, $oldVersion)
    {
        $this->elementVersion = $elementVersion;
        $this->values = $values;
        $this->language = $language;
        $this->oldVersion = $oldVersion;
    }

    /**
     * @return ElementVersion
     */
    public function getElementVersion()
    {
        return $this->elementVersion;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
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
