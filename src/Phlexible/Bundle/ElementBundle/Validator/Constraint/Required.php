<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Element validator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Required extends Constraint
{
    public $message = 'This value is required.';
}
