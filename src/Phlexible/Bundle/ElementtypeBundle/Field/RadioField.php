<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Field;

/**
 * Radio field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RadioField extends AbstractField
{
    protected $hasOptions = true;
    protected $icon       = 'p-elementtype-field_radio-icon';
}
