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
     * @param PatternResourceLocator $locator
     */
    public function __construct(PatternResourceLocator $locator)
    {
        $this->locator = $locator;

        $this->parser = new XmlParser($this);
    }

    /**
     * {@inheritdoc}
     */
    public function loadAll()
    {
        $files = $this->locator->locate('*.xml', 'elementtypes', false);

        $elementtypes = array();
        foreach ($files as $file) {
            $elementtypes[] = $this->loadFile($file);
        }

        return $elementtypes;
    }

    /**
     * {@inheritdoc}
     */
    public function load($elementtypeId)
    {
        $filename = $this->locator->locate("$elementtypeId.xml", 'elementtypes', true);

        return $this->loadFile($filename);
    }

    /**
     * {@inheritdoc}
     *
     * @return \SimpleXMLElement
     */
    public function open($elementtypeId)
    {
        $filename = $this->locator->locate("$elementtypeId.xml", 'elementtypes', true);

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
