<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Model;

use Phlexible\Bundle\TeaserBundle\Entity\ElementCatch;

/**
 * TeaCatchser manager interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface CatchManagerInterface
{
    /**
     * @param int $id
     *
     * @return ElementCatch
     */
    public function findCatch($id);

    /**
     * @param ElementCatch $catch
     */
    public function updateCatch(ElementCatch $catch);

    /**
     * @param ElementCatch $catch
     */
    public function deleteCatch(ElementCatch $catch);
}