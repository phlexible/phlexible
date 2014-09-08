<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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