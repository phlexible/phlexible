<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementtypeBundle\Field;

/**
 * Base field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class Field
{
    /**
     * @var bool
     */
    protected $isField = false;

    /**
     * @var bool
     */
    protected $isContainer = false;

    /**
     * @return string
     */
    public function getIcon()
    {
        return 'p-elementtypes-field_fallback.gif';
    }

    /**
     * @return bool
     */
    abstract public function isContainer();

    /**
     * @return bool
     */
    abstract public function isField();

    /**
     * @return bool
     */
    public function hasContent()
    {
        return !$this->isContainer();
    }
}
