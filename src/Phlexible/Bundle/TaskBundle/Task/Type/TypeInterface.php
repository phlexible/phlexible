<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TaskBundle\Task\Type;

use Phlexible\Bundle\TaskBundle\Entity\Task;

/**
 * Type interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface TypeInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getComponent();

    /**
     * @return string
     */
    public function getResource();

    /**
     * @param Task $task
     *
     * @return string
     */
    public function getTitle(Task $task);

    /**
     * @param Task $task
     *
     * @return string
     */
    public function getText(Task $task);

    /**
     * @param Task $task
     *
     * @return string
     */
    public function getLink(Task $task);

    /**
     * @param Task $task
     *
     * @return array
     */
    public function getMenuHandle(Task $task);
}
