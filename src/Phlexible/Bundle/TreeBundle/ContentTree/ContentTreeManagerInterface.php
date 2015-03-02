<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\ContentTree;

/**
 * Content tree manager interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ContentTreeManagerInterface
{
    /**
     * @return ContentTreeInterface[]
     */
    public function findAll();

    /**
     * @param string $siterootId
     *
     * @return ContentTreeInterface
     */
    public function find($siterootId);

    /**
     * @param int $treeId
     *
     * @return ContentTreeInterface|null
     */
    public function findByTreeId($treeId);
}
