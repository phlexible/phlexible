<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\ContentTree;

/**
 * XML content tree manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlContentTreeManager implements ContentTreeManagerInterface
{
    /**
     * @var string
     */
    private $xmlDir;

    /**
     * @var XmlContentTree[]
     */
    private $trees;

    /**
     * @param string $xmlDir
     */
    public function __construct($xmlDir)
    {
        $this->xmlDir = $xmlDir;
    }

    /**
     * @return XmlContentTree[]
     */
    public function findAll()
    {
        if ($this->trees === null) {
            $xmlFiles = glob($this->xmlDir . '*.xml');

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
        if (!$trees) {
            return null;
        }

        foreach ($trees as $tree) {
            if ($tree->has($treeId)) {
                return $tree;
            }
        }

        return null;
    }
}
