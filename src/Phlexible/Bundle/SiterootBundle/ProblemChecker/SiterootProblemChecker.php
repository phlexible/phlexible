<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\ProblemChecker;

use Phlexible\Bundle\ProblemBundle\Entity\Problem;
use Phlexible\Bundle\ProblemBundle\ProblemChecker\ProblemCheckerInterface;
use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;

/**
 * Siteroot problem checker
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiterootProblemChecker implements ProblemCheckerInterface
{
    /**
     * @var SiterootManagerInterface
     */
    private $siterootManager;

    /**
     * @param SiterootManagerInterface $siterootManager
     */
    public function __construct(SiterootManagerInterface $siterootManager)
    {
        $this->siterootManager = $siterootManager;
    }

    /**
     * Check for problems
     *
     * @return mixed
     */
    public function check()
    {
        $siteroots = $this->siterootManager->findAll();

        if (!count($siteroots)) {
            $problem = new Problem();
            $problem
                ->setId('siteroots_no_siteroots')
                ->setCheckClass(__CLASS__)
                ->setSeverity(Problem::SEVERITY_WARNING)
                ->setMessage('No Siteroots defined.')
                ->setHint('Add at least one siteroot.')
                ->setIconClass('p-siteroot-component-icon');

            return [$problem];
        }

        foreach ($siteroots as $siteRoot) {
            if (!$siteRoot->getNavigations()) {
                $problem = new Problem();
                $problem
                    ->setId('siteroots_no_navigation_' . $siteRoot->getId())
                    ->setCheckClass(__CLASS__)
                    ->setSeverity(Problem::SEVERITY_WARNING)
                    ->setMessage("No Navigation defined for Siteroot {$siteRoot->getTitle()}.")
                    ->setHint('Add Navigations to the Siteroot.')
                    ->setIconClass('p-siteroot-component-icon');
                $problems[] = $problem;
            }

            $specialTids = $siteRoot->getSpecialTids();
            if (!$specialTids) {
                $problem = new Problem();
                $problem
                    ->setId("siteroots_no_specialtids_{$siteRoot->getId()}")
                    ->setCheckClass(__CLASS__)
                    ->setSeverity(Problem::SEVERITY_WARNING)
                    ->setMessage("No Special TIDs defined for Siteroot {$siteRoot->getTitle()}.")
                    ->setHint('Add Special TIDs to the Siteroot.')
                    ->setIconClass('p-siteroot-component-icon');
                $problems[] = $problem;
            } else {
                // TODO: repair
                /*
                $treeManager = Makeweb_Elements_Tree_Manager::getInstance();

                foreach ($specialTids as $specialTidLanguage => $specialTidValues) {
                    foreach ($specialTidValues as $specialTidKey => $specialTid) {
                        try {
                            $node = $treeManager->getNodeByNodeId($specialTid);

                            if ($node->getTree()->getSiterootId() !== $siteRoot->getId()) {
                                $problem = new Problem();
                                $problem
                                    ->setId('siteroots_inconsistant_tid_' . $specialTidKey.'_' . $siteRoot->getId())
                                    ->setCheckClass(__CLASS__)
                                    ->setSeverity(Problem::SEVERITY_WARNING)
                                    ->setMessage("Special TID $specialTidKey from Siteroot {$siteRoot->getTitle()} has TID $specialTid from wrong Siteroot {$node->getTree()->getSiteroot()->getTitle()}.")
                                    ->setHint("Set new value for Special TIDs $specialTidKey in the Siteroot")
                                    ->setIconClass('p-siteroot-component-icon')
                                ;
                                $problems[] = $problem;
                            }
                        } catch (\Exception $e) {
                            $problem = new Problem();
                            $problem
                                ->setId('siteroots_unknown_tid_' . $specialTidKey.'_' . $siteRoot->getId())
                                ->setCheckClass(__CLASS__)
                                ->setSeverity(Problem::SEVERITY_WARNING)
                                ->setMessage("Special TID $specialTidKey has unknown TID $specialTid in Siteroot {$siteRoot->getTitle()}.")
                                ->setHint("Set new value for Special TIDs $specialTidKey in the Siteroot.")
                                ->setIconClass('p-siteroot-component-icon')
                            ;
                            $problems[] = $problem;
                        }
                    }
                }
                */
            }

            if (!$siteRoot->getTitles()) {
                $problem = new Problem();
                $problem
                    ->setId("siteroots_no_titles_{$siteRoot->getId()}")
                    ->setCheckClass(__CLASS__)
                    ->setSeverity(Problem::SEVERITY_WARNING)
                    ->setMessage("No Titles defined for Siteroot {$siteRoot->getId()}.")
                    ->setHint('Set Titles for the Siteroot')
                    ->setIconClass('p-siteroot-component-icon');
                $problems[] = $problem;
            }

            if (!$siteRoot->getUrls()) {
                $problem = new Problem();
                $problem
                    ->setId("siteroots_no_urls_{$siteRoot->getId()}")
                    ->setCheckClass(__CLASS__)
                    ->setSeverity(Problem::SEVERITY_WARNING)
                    ->setMessage("No Urls defined for Siteroot {$siteRoot->getTitle('en')}.")
                    ->setHint('Set Urls for the Siteroot')
                    ->setIconClass('p-siteroot-component-icon');
                $problems[] = $problem;
            }
        }

        /*
        $siterootOverrides = false;
        if ($container->getParam(':phlexible_siteroot.overrides'))
        {
            foreach ($container->getParam(':phlexible_siteroot.overrides') as $siteroot)
            {
                if (array_key_exists('navigation', $siteroot))
                {
                    $siterootOverrides = true;
                    break;
                }
            }
        }

        if (!MWF_Env::isDev() && $siterootOverrides)
        {
            $problem = new Problem(
                Problem::SEVERITY_WARNING,
                'Running in live mode, but there are siteroot overrides defined. On staging servers this might be valid.',
                'Remove siteroot overrides.'
            );
            $problem->iconCls = 'p-siteroot-component-icon';
            $problems[] = $problem;
        }
        */

        return $problems;
    }
}
