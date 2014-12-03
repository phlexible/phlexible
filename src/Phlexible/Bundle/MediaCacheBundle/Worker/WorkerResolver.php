<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Worker;

use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;
use Phlexible\Bundle\MediaTemplateBundle\Model\TemplateInterface;

/**
 * Cache worker resolver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class WorkerResolver
{
    /**
     * @var WorkerInterface[]
     */
    private $workers = [];

    /**
     * @param WorkerInterface[] $workers
     */
    public function __construct(array $workers = [])
    {
        foreach ($workers as $worker) {
            $this->addWorker($worker);
        }
    }

    /**
     * @param WorkerInterface $worker
     *
     * @return $this
     */
    public function addWorker(WorkerInterface $worker)
    {
        $this->workers[] = $worker;

        return $this;
    }

    /**
     * Determine and return worker classname
     *
     * @param TemplateInterface     $template
     * @param ExtendedFileInterface $file
     *
     * @return WorkerInterface
     */
    public function resolve(TemplateInterface $template, ExtendedFileInterface $file)
    {
        foreach ($this->workers as $worker) {
            if ($worker->accept($template, $file)) {
                return $worker;
            }
        }

        return null;
    }

}
