<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Task\Type;

/**
 * General element task type
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GeneralType extends AbstractType
{
    protected $_generic = true;

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'elements.generic';
    }

    /**
     * Get required parameters for this task
     *
     * @return array
     */
    public function getRequiredParameters()
    {
        return array('tid');
    }

    /**
     * @return string
     */
    protected function getTitleKey()
    {
        return 'elements.task_general';
    }

    /**
     * @return string
     */
    protected function getTextKey()
    {
        return 'elements.task_general_template';
    }
}