<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Task\Type;

/**
 * Publish element task type
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PublishType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'elements.publish';
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
        return 'elements.task_publish_element';
    }

    /**
     * @return string
     */
    protected function getTextKey()
    {
        return 'elements.task_publish_element_template';
    }
}