<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\CmsBundle\ProblemChecker;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\ProblemBundle\Entity\Problem;
use Phlexible\Bundle\ProblemBundle\ProblemChecker\ProblemCheckerInterface;

/**
 * Special TID problem checker.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SpecialTidChecker implements ProblemCheckerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

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
        $problems = [];

        foreach ($this->entityManager->getRepository('PhlexibleSiterootBundle:Siteroot')->findAll() as $siteroot) {
            if (!$siteroot->getSpecialTid(null, 'error_404')) {
                $problem = new Problem();
                $problem
                    ->setId('siteroot_'.$siteroot->getId().'_error_404_missing')
                    ->setCheckClass(__CLASS__)
                    ->setIconClass('p-frontend-component-icon')
                    ->setSeverity(Problem::SEVERITY_CRITICAL)
                    ->setMessage("No special tid for 404 page found in siteroot {$siteroot->getTitle()}.")
                    ->setHint('Create a special tid "error_404"');

                $problems[] = $problem;
            }

            if (!$siteroot->getSpecialTid(null, 'error_500')) {
                $problem = new Problem();
                $problem
                    ->setId('siteroot_'.$siteroot->getId().'_error_500_missing')
                    ->setCheckClass(__CLASS__)
                    ->setIconClass('p-frontend-component-icon')
                    ->setSeverity(Problem::SEVERITY_CRITICAL)
                    ->setMessage("No special tid for 500 page found in siteroot {$siteroot->getTitle()}.")
                    ->setHint('Create a special tid "error_500"');

                $problems[] = $problem;
            }
        }

        return $problems;
    }
}
