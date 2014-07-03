<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ProblemBundle\Portlet;

use Phlexible\Bundle\DashboardBundle\Portlet\Portlet;
use Phlexible\Bundle\ProblemBundle\Entity\Problem;
use Phlexible\Bundle\ProblemBundle\Problem\ProblemFetcher;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Problems portlet
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @author Phillip Look <pl@brainbits.net>
 */
class ProblemsPortlet extends Portlet
{
    /**
     * @var ProblemFetcher
     */
    private $fetcher;

    /**
     * @param TranslatorInterface $translator
     * @param ProblemFetcher      $fetcher
     */
    public function __construct(TranslatorInterface $translator, ProblemFetcher $fetcher)
    {
        $this
            ->setId('problems-portlet')
            ->setTitle($translator->trans('problems.problems', array(), 'gui'))
            ->setClass('Phlexible.problems.portlet.Problems')
            ->setIconClass('p-problem-portlet-icon')
            ->setResource('problems');

        $this->fetcher = $fetcher;
    }

    /**
     * Return Portlet data
     *
     * @return array
     */
    public function getData()
    {
        $data = array();

        $problems = $this->fetcher->fetch();

        $allowedSeverities = array(
            Problem::SEVERITY_CRITICAL,
            Problem::SEVERITY_WARNING,
        );

        foreach ($problems as $problem) {
            if (!in_array($problem->getSeverity(), $allowedSeverities)) {
                continue;
            }

            $data[] = array(
                'id'       => strlen($problem->getId()) ? $problem->getId() : md5(serialize($problem)),
                'iconCls'  => $problem->getIconClass(),
                'severity' => $problem->getSeverity(),
                'msg'      => $problem->getMessage(),
                'hint'     => $problem->getHint(),
                'link'     => $problem->getLink(),
            );
        }

        if (!count($data)) {
            $data = false;
        }

        return $data;
    }
}
