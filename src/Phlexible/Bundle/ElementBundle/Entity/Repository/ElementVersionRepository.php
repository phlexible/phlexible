<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\ElementBundle\Entity\Element;

/**
 * Element version repository
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementVersionRepository extends EntityRepository
{
    /**
     * @param Element $element
     *
     * @return array
     */
    public function getVersions(Element $element)
    {
        $conn = $this->getEntityManager()->getConnection();

        $qb = $conn->createQueryBuilder();
        $qb
            ->select('ev.version')
            ->from('element_version', 'ev')
            ->where($qb->expr()->eq('ev.eid', $element->getEid()));

        $statement = $conn->executeQuery($qb->getSQL());

        $versions = [];
        while ($version = $statement->fetchColumn()) {
            $versions[] = (int) $version;
        }

        return $versions;
    }
}
