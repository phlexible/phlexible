<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\File\Loader;

use Phlexible\Bundle\ElementtypeBundle\File\Parser\XmlParser;
use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;
use Phlexible\Bundle\GuiBundle\Locator\PatternResourceLocator;

/**
 * XML loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlLoader implements LoaderInterface
{
    /**
     * @var PatternResourceLocator
     */
    private $locator;

    /**
     * @var XmlParser
     */
    private $parser;

    /**
     * @var array
     */
    private $idMap;

    /**
     * @var array
     */
    private $uniqueIdMap;

    /**
     * @param PatternResourceLocator $locator
     */
    public function __construct(PatternResourceLocator $locator)
    {
        $this->locator = $locator;

        $this->parser = new XmlParser($this);
    }

    private function createMap()
    {
        if ($this->idMap !== null)  {
            return;
        }

        $files = $this->locator->locate('*.xml', 'elementtypes', false);

        $idMap = array();
        $uniqueIdMap = array();
        foreach ($files as $file) {
            $xml = simplexml_load_file($file);
            $rootAttributes = $xml->attributes();
            $id = (string) $rootAttributes['id'];
            $uniqueId = (string) $rootAttributes['uniqueId'];

            $idMap[$id] = $file;
            $uniqueIdMap[$uniqueId] = $file;
        }

        $this->idMap = $idMap;
        $this->uniqueIdMap = $uniqueIdMap;
    }

    /**
     * {@inheritdoc}
     */
    public function loadAll()
    {
        $this->createMap();

        $elementtypes = array();
        foreach ($this->idMap as $id => $file) {
            $elementtypes[] = $this->loadFile($file);
        }

        return $elementtypes;
    }

    /**
     * {@inheritdoc}
     */
    public function load($elementtypeId)
    {
        $this->createMap();

        $filename = $this->idMap[$elementtypeId];

        return $this->loadFile($filename);
    }

    /**
     * {@inheritdoc}
     *
     * @return \SimpleXMLElement
     */
    public function open($elementtypeId)
    {
        $filename = $this->idMap[$elementtypeId];

        return simplexml_load_file($filename);
    }

    /**
     * @param string $filename
     *
     * @return Elementtype
     */
    private function loadFile($filename)
    {
        return $this->parser->parse(simplexml_load_file($filename));
    }
}
