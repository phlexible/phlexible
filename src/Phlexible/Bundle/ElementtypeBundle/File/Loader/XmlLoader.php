<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\File\Loader;

use FluentDOM\Document;
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

        $files = array();
        foreach ($this->locator->locate('*.xml', 'elementtypes', false) as $file) {
            $name = basename($file);
            if (!isset($files[$name])) {
                $files[$name] = $file;
            }
        }

        $idMap = array();
        foreach ($files as $file) {
            $xml = simplexml_load_file($file);
            $rootAttributes = $xml->attributes();
            $id = (string) $rootAttributes['id'];

            $idMap[$id] = $file;
        }

        $this->idMap = $idMap;
    }

    /**
     * {@inheritdoc}
     */
    public function loadAll()
    {
        $this->createMap();

        $elementtypes = array();
        foreach ($this->idMap as $id => $file) {
            $elementtypes[] = $this->parse($this->loadFile($file));
        }

        return $elementtypes;
    }

    /**
     * {@inheritdoc}
     */
    public function load($elementtypeId)
    {
        return $this->parse($this->loadElementtype($elementtypeId));
    }

    /**
     * @param string $elementtypeId
     *
     * @return Document
     */
    private function loadElementtype($elementtypeId)
    {
        $this->createMap();

        $filename = $this->idMap[$elementtypeId];

        return $this->loadFile($filename);
    }

    /**
     * {@inheritdoc}
     *
     * @return Document
     */
    private function loadFile($filename)
    {
        $dom = new Document();
        $dom->formatOutput = true;
        $dom->load($filename);

        $this->applyReferenceElementtype($dom);

        return $dom;
    }

    /**
     * @param Document $dom
     */
    private function applyReferenceElementtype(Document $dom)
    {
        foreach ($dom->xpath()->evaluate('//node[@referenceElementtypeId]') as $node) {
            /* @var $node \FluentDOM\Element */
            $referenceElementtypeId = $node->getAttribute('referenceElementtypeId');
            $referenceDom = $this->loadElementtype($referenceElementtypeId);
            foreach ($referenceDom->documentElement->evaluate('structure[1]/node') as $referenceNode) {
                $node->appendElement('references')->append($referenceNode);
            }
        }
    }

    /**
     * @param Document $dom
     *
     * @return Elementtype
     */
    private function parse(Document $dom)
    {
        return $this->parser->parse($dom);
    }
}
