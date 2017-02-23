<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaManagerBundle\EventListener;

use Phlexible\Component\Volume\VolumeManager;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Ensure media manager initialized before filter listener.
 *
 * @author Tim Hoepfner <thoepfner@brainbits.net>
 */
class EnsureMediaManagerInitializedBeforeFilterListener
{
    /**
     * @var VolumeManager
     */
    private $volumeManager;

    /**
     * EnsureMediaManagerInitializedBeforeFilter constructor.
     *
     * @param VolumeManager $volumeManager
     */
    public function __construct(VolumeManager $volumeManager)
    {
        $this->volumeManager = $volumeManager;
    }

    /**
     * @param FilterControllerEvent $event
     *
     * @throws \Exception
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
            return;
        }
        $fqcn = get_class($controller[0]);
        $isMediaManagerBundleController = $this->startsWith($fqcn, 'Phlexible\Bundle\MediaManagerBundle\Controller');
        if ($isMediaManagerBundleController && !$this->checkDefaultVolumeExists()) {
            throw new \Exception('No default volume found. Maybe you haven\'t initialized the MediaManager yet?');
        }
    }

    private function checkDefaultVolumeExists()
    {
        try {
            $this->volumeManager->get('default');

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Determine if a given string starts with a given substring.
     *
     * @param string       $haystack
     * @param string|array $needles
     *
     * @return bool
     */
    private function startsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && mb_strpos($haystack, $needle) === 0) {
                return true;
            }
        }

        return false;
    }
}
