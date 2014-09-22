<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter;

use Doctrine\DBAL\Query\QueryBuilder;
use Phlexible\Bundle\ElementFinderBundle\Entity\ElementFinderConfig;

/**
 * Query enhancer interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface QueryEnhancerInterface
{
    /**
     * Add filter to a select statement.
     *
     * @param ElementFinderConfig $catch
     * @param QueryBuilder        $qb
     */
    public function enhance(ElementFinderConfig $catch, QueryBuilder $qb);
}
