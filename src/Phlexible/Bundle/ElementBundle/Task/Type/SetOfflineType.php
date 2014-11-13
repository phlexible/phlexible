<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Task\Type;

/**
 * Set element offline task type
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SetOfflineType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'elements.set_offline';
    }

    /**
     * Get required parameters for this task
     *
     * @return array
     */
    public function getRequiredParameters()
    {
        return array('type', 'type_id', 'language');
    }

    /**
     * Return the task resource
     *
     * @return string
     */
    public function getResource()
    {
        return 'elements_publish';
    }

    /**
     * @return string
     */
    protected function getTitleKey()
    {
        return 'elements.task_set_element_offline';
    }

    /**
     * @return string
     */
    protected function getTextKey()
    {
        return 'elements.task_set_element_offline_template';
    }
}
