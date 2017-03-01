<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MessageBundle\DataCollector;

use Phlexible\Bundle\MessageBundle\Handler\DebugHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\DataCollector\LateDataCollectorInterface;

/**
 * Messages data collector.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MessagesDataCollector extends DataCollector implements LateDataCollectorInterface
{
    private $debugHandler;

    /**
     * @param null $debugHandler
     */
    public function __construct($debugHandler = null)
    {
        if (null !== $debugHandler && $debugHandler instanceof DebugHandler) {
            $this->debugHandler = $debugHandler;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        // everything is done as late as possible
    }

    /**
     * {@inheritdoc}
     */
    public function lateCollect()
    {
        if (null !== $this->debugHandler) {
            $this->data = $this->computeErrorsCount();
            $this->data['messages'] = $this->sanitizeMessages();
        }
    }

    /**
     * Gets the called events.
     *
     * @return array An array of called events
     *
     * @see TraceableEventDispatcherInterface
     */
    public function countErrors()
    {
        return isset($this->data['error_count']) ? $this->data['error_count'] : 0;
    }

    /**
     * Gets the messages.
     *
     * @return array An array of messages
     */
    public function getMessages()
    {
        return isset($this->data['messages']) ? $this->data['messages'] : [];
    }

    /**
     * @return array
     */
    public function getPriorities()
    {
        return isset($this->data['priorities']) ? $this->data['priorities'] : [];
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return isset($this->data['types']) ? $this->data['types'] : [];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'messages';
    }

    private function sanitizeMessages()
    {
        $messages = $this->debugHandler->getMessages();

        return $messages;
    }

    private function computeErrorsCount()
    {
        $count = [
            'error_count' => 0,
            'priorities' => [],
            'types' => [],
        ];

        foreach ($this->debugHandler->getMessages() as $message) {
            if (isset($count['priorities'][$message['priority']])) {
                ++$count['priorities'][$message['priority']]['count'];
            } else {
                $count['priorities'][$message['priority']] = [
                    'count' => 1,
                    'name' => $message['priorityName'],
                ];
            }

            if (isset($count['types'][$message['type']])) {
                ++$count['types'][$message['type']]['count'];
            } else {
                $count['types'][$message['type']] = [
                    'count' => 1,
                    'name' => $message['typeName'],
                ];
            }
            if ($message['type'] === 'error') {
                ++$count['error_count'];
            }
        }

        ksort($count['priorities']);
        ksort($count['types']);

        return $count;
    }
}
