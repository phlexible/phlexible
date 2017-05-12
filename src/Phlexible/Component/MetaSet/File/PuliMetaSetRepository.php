<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MetaSet\File;

use Phlexible\Component\MetaSet\Domain\MetaSetCollection;
use Phlexible\Component\MetaSet\File\Dumper\DumperInterface;
use Phlexible\Component\MetaSet\File\Parser\ParserInterface;
use Phlexible\Component\MetaSet\Model\MetaSetInterface;
use Puli\Discovery\Api\EditableDiscovery;
use Puli\Repository\Api\EditableRepository;
use Puli\Repository\Resource\FileResource;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Puli meta set repository.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PuliMetaSetRepository implements MetaSetRepositoryInterface
{
    /**
     * @var EditableDiscovery
     */
    private $puliDiscovery;

    /**
     * @var EditableRepository
     */
    private $puliRepository;

    /**
     * @var string
     */
    private $dumpDir;

    /**
     * @var string
     */
    private $defaultDumpType;

    /**
     * @var string
     */
    private $puliResourceDir;

    /**
     * @var ParserInterface[]
     */
    private $parsers = array();

    /**
     * @var DumperInterface[]
     */
    private $dumpers = array();

    /**
     * @var MetaSetCollection
     */
    private $metasets;

    /**
     * @param EditableDiscovery  $puliDiscovery
     * @param EditableRepository $puliRepository
     * @param string             $defaultDumpType
     * @param string             $dumpDir
     * @param string             $puliResourceDir
     */
    public function __construct(
        EditableDiscovery $puliDiscovery,
        EditableRepository $puliRepository,
        $defaultDumpType,
        $dumpDir,
        $puliResourceDir
    ) {
        $this->puliDiscovery = $puliDiscovery;
        $this->puliRepository = $puliRepository;
        $this->defaultDumpType = $defaultDumpType;
        $this->dumpDir = $dumpDir;
        $this->puliResourceDir = $puliResourceDir;
    }

    /**
     * @param string          $type
     * @param ParserInterface $parser
     *
     * @return $this
     */
    public function addParser($type, ParserInterface $parser)
    {
        $this->parsers[$type] = $parser;

        return $this;
    }

    /**
     * @param string          $type
     * @param DumperInterface $dumper
     *
     * @return $this
     */
    public function addDumper($type, DumperInterface $dumper)
    {
        $this->dumpers[$type] = $dumper;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function loadAll()
    {
        if ($this->metasets === null) {
            $this->metasets = new MetaSetCollection();

            foreach ($this->puliDiscovery->findBindings('phlexible/metasets') as $binding) {
                foreach ($binding->getResources() as $resource) {
                    $extension = pathinfo($resource->getFilesystemPath(), PATHINFO_EXTENSION);
                    if (!isset($this->parsers[$extension])) {
                        continue;
                    }
                    $parser = $this->parsers[$extension];
                    $this->metasets->add($parser->parse($resource->getBody()));
                }
            }
        }

        return $this->metasets;
    }

    /**
     * {@inheritdoc}
     */
    public function load($id)
    {
        $metaSets = $this->loadAll();

        return $metaSets->get($id);
    }

    /**
     * {@inheritdoc}
     */
    public function loadByName($name)
    {
        $metaSets = $this->loadAll();

        return $metaSets->getByName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function writeMetaSet(MetaSetInterface $metaSet, $type = null)
    {
        if (!$type) {
            $type = $this->defaultDumpType;
        }

        $dumper = $this->dumpers[$type];
        $content = $dumper->dump($metaSet);

        $filename = strtolower("{$metaSet->getId()}.{$type}");
        $path = "{$this->dumpDir}/$filename";
        $filesystem = new Filesystem();
        $filesystem->dumpFile($path, $content);

        $resourcePath = "{$this->puliResourceDir}/$filename";
        $resource = new FileResource($path, $resourcePath);
        $this->puliRepository->add($resourcePath, $resource);
    }
}
