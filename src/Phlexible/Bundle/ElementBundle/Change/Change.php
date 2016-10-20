<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Change;

use Phlexible\Bundle\ElementBundle\Entity\ElementSource;
use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;

/**
 * Elementtype change
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Change
{
    /**
     * @var ElementSource[]
     */
    private $outdatedElementSources = [];

    /**
     * @var bool
     */
    private $needImport = false;

    /**
     * @var string
     */
    private $reason;

    /**
     * @var Elementtype
     */
    private $elementtype;

    /**
     * @param Elementtype     $elementtype
     * @param bool            $needImport
     * @param string          $reason
     * @param ElementSource[] $outdatedElementSources
     */
    public function __construct(Elementtype $elementtype, $needImport, $reason, array $outdatedElementSources = [])
    {
        $this->elementtype = $elementtype;
        $this->needImport = $needImport;
        $this->reason = $reason;

        foreach ($outdatedElementSources as $outdatedElementSource) {
            $this->addOutdatedElementSource($outdatedElementSource);
        }
    }

    /**
     * @return ElementSource[]
     */
    public function getOutdatedElementSources()
    {
        return $this->outdatedElementSources;
    }

    /**
     * @return bool
     */
    public function getNeedImport()
    {
        return $this->needImport;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param ElementSource $outdatedElementSource
     *
     * @return $this
     */
    public function addOutdatedElementSource(ElementSource $outdatedElementSource)
    {
        $this->outdatedElementSources[] = $outdatedElementSource;

        return $this;
    }

    /**
     * @return Elementtype
     */
    public function getElementtype()
    {
        return $this->elementtype;
    }
}
