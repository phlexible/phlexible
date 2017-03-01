<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaCache\ImageDelegate;

use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Phlexible\Component\MediaType\Model\MediaType;

/**
 * Delegate worker.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DelegateService
{
    /**
     * @var DelegateWorker
     */
    private $worker;

    /**
     * @param DelegateWorker $worker
     */
    public function __construct(DelegateWorker $worker)
    {
        $this->worker = $worker;
    }

    /**
     * @return DelegateWorker
     */
    public function getWorker()
    {
        return $this->worker;
    }

    /**
     * @param TemplateInterface $template
     * @param MediaType         $mediaType
     * @param bool              $createOnDemand
     *
     * @return string
     */
    public function getClean(TemplateInterface $template, MediaType $mediaType, $createOnDemand = true)
    {
        $filename = $this->worker->getCleanFilename($template, $mediaType);

        if (file_exists($filename)) {
            return $filename;
        }

        if (!$createOnDemand) {
            return null;
        }

        $this->worker->write($mediaType, $template);

        return $filename;
    }

    /**
     * @param TemplateInterface $template
     * @param MediaType         $mediaType
     * @param bool              $createOnDemand
     *
     * @return string
     */
    public function getWaiting(TemplateInterface $template, MediaType $mediaType, $createOnDemand = true)
    {
        $filename = $this->worker->getWaitingFilename($template, $mediaType);

        if (file_exists($filename)) {
            return $filename;
        }

        if (!$createOnDemand) {
            return null;
        }

        $this->worker->write($mediaType, $template);

        return $filename;
    }
}
