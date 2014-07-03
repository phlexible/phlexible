<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\MetaSet;

use Phlexible\Bundle\MetaSetBundle\Exception\NotFoundException;

/**
 * Meta sets repository
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaSetRepository
{
    /**
     * @var MetaSetLoader
     */
    private $loader;

    /**
     * @var MetaSetDumper
     */
    private $dumper;

    /**
     * @var MetaSetCollection
     */
    private $metaSets;

    /**
     * @param MetaSetLoader $loader
     * @param MetaSetDumper $dumper
     */
    public function __construct(
        MetaSetLoader $loader,
        MetaSetDumper $dumper)
    {
        $this->loader = $loader;
        $this->dumper = $dumper;
    }

    /**
     * @return MetaSetCollection
     */
    public function getCollection()
    {
        if ($this->metaSets === null) {
            $this->metaSets = $this->loader->loadMetaSets();
        }

        return $this->metaSets;
    }

    /**
     * Find meta set
     *
     * @param string $id
     *
     * @return MetaSet
     * @throws NotFoundException
     */
    public function find($id)
    {
        $metaSet = $this->getCollection()->get($id);

        if (null !== $metaSet) {
            return $metaSet;
        }

        $metaSet = $this->getCollection()->getByTitle($id);

        if (null !== $metaSet) {
            return $metaSet;
        }

        throw new NotFoundException('Meta set not found.');
    }

    /**
     * Return all meta sets
     *
     * @return MetaSet[]
     */
    public function findAll()
    {
        return $this->getCollection()->getAll();
    }

    /**
     * Save meta set
     *
     * @param MetaSet $metaSets
     */
    public function save(MetaSet $metaSets)
    {
        // raise revision
        $metaSets->setRevision($metaSets->getRevision() + 1);

        $this->dumper->dumpTemplate($metaSets);
    }
}
