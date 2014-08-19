<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\ContentTree;

/**
 * Content tree manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ContentTreeManager
{
    /**
     * @var XmlContentTree[]
     */
    private $trees = null;

    /**
     * @return XmlContentTree[]
     */
    public function findAll()
    {
        if ($this->trees === null) {
            $xmlFiles = glob('/tmp/*.xml');

            foreach ($xmlFiles as $xmlFile) {
                $this->trees[] = new XmlContentTree($xmlFile);
            }
        }

        return $this->trees;
    }

    /**
     * @param int $treeId
     *
     * @return null|XmlContentTree
     */
    public function findByTreeId($treeId)
    {
        $trees = $this->findAll();
        if (!$trees) return null;

        foreach ($trees as $tree) {
            if ($tree->has($treeId)) {
                return $tree;
            }
        }

        return null;
    }
}
