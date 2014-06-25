<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendBundle\ProblemChecker;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\ProblemBundle\Entity\Problem;
use Phlexible\Bundle\ProblemBundle\ProblemChecker\ProblemCheckerInterface;

/**
 * Special TID problem checker
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SpecialTidChecker implements ProblemCheckerInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        $problems = array();

        foreach ($this->entityManager->getRepository('PhlexibleSiterootBundle:Siteroot')->findAll() as $siteroot) {
            if (!$siteroot->getSpecialTid(null, 'error_404')) {
                $problem = new Problem();
                $problem
                    ->setId('siteroot_' . $siteroot->getId() . '_error_404_missing')
                    ->setCheckClass(__CLASS__)
                    ->setIconClass('p-frontend-component-icon')
                    ->setSeverity(Problem::SEVERITY_CRITICAL)
                    ->setMessage("No special tid for 404 page found in siteroot {$siteroot->getTitle()}.")
                    ->setHint('Create a special tid "error_404"')
                ;

                $problems[] = $problem;
            }

            if (!$siteroot->getSpecialTid(null, 'error_500')) {
                $problem = new Problem();
                $problem
                    ->setId('siteroot_' . $siteroot->getId() . '_error_500_missing')
                    ->setCheckClass(__CLASS__)
                    ->setIconClass('p-frontend-component-icon')
                    ->setSeverity(Problem::SEVERITY_CRITICAL)
                    ->setMessage("No special tid for 500 page found in siteroot {$siteroot->getTitle()}.")
                    ->setHint('Create a special tid "error_500"')
                ;

                $problems[] = $problem;
            }
        }

        return $problems;
    }
}