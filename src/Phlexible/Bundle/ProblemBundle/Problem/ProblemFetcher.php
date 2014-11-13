<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ProblemBundle\Problem;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\ProblemBundle\Entity\Problem;
use Phlexible\Bundle\ProblemBundle\Event\CollectProblemsEvent;
use Phlexible\Bundle\ProblemBundle\ProblemEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Problems aggregator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ProblemFetcher
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param EntityManager            $entityManager
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EntityManager $entityManager,
                                EventDispatcherInterface $dispatcher)
    {
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return Problem[]
     */
    public function fetch()
    {
        $event = new CollectProblemsEvent();
        $this->dispatcher->dispatch(ProblemEvents::COLLECT, $event);

        $problemsRepository = $this->entityManager->getRepository('PhlexibleProblemBundle:Problem');

        $liveProblems = $event->getProblems();
        foreach ($liveProblems as $liveProblem) {
            $liveProblem->setLive(true);
        }
        $cachedProblems = $problemsRepository->findAll();

        return $liveProblems + $cachedProblems;
    }
}
