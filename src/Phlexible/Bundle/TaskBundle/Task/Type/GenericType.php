<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TaskBundle\Task\Type;

use Phlexible\Bundle\TaskBundle\Entity\Task;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Generic formatter
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GenericType implements TypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'generic';
    }

    /**
     * {@inheritdoc}
     */
    public function getComponent()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getResource()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(Task $task)
    {
        $title = 'Allgemeine Aufgabe';//$this->translator->trans('tasks.general_title');

        return $title;
    }

    /**
     * {@inheritdoc}
     */
    public function getText(Task $task)
    {
        $text = 'Allgemeine Aufgabe "{title}".'; //$this->translator->trans('tasks.general_template');

        $replace = ['{title}' => $this->getTitle($task)];

        $text = str_replace(array_keys($replace), array_values($replace), $text);

        return $text;
    }

    /**
     * {@inheritdoc}
     */
    public function getLink(Task $task)
    {
        $mailLink = '?e=tasks&p[id]=' . $task->getId();

        return $mailLink;
    }

    /**
     * {@inheritdoc}
     */
    public function getMenuHandle(Task $task)
    {
        $menuHandle = [
            'xtype' => 'tasks'
        ];

        return $menuHandle;
    }
}
