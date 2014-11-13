<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Element;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;

/**
 * Element hasher
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementHasher
{
    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var string
     */
    private $algo;

    /**
     * @param ElementService $elementService
     * @param string         $algo
     */
    public function __construct(ElementService $elementService, $algo = 'md5')
    {
        $this->elementService = $elementService;
        $this->algo = $algo;
    }

    /**
     * @return string
     */
    public function getAlgo()
    {
        return $this->algo;
    }

    /**
     * @param int    $eid
     * @param int    $version
     * @param string $language
     *
     * @return array
     */
    public function createHashValuesByEid($eid, $version, $language)
    {
        $element = $this->elementService->findElement($eid);
        $elementVersion = $this->elementService->findElementVersion($element, $version);

        return $this->createHashValuesByElementVersion($elementVersion, $language);
    }

    /**
     * @param ElementVersion $elementVersion
     * @param string         $language
     *
     * @return array
     */
    public function createHashValuesByElementVersion(ElementVersion $elementVersion, $language)
    {
        $elementStructure = $this->elementService->findElementStructure($elementVersion, $language);

        $eid = $elementVersion->getElement()->getEid();
        $elementtypeId = $elementVersion->getElement()->getElementtypeId();
        $elementtypeVersion = $elementVersion->getElementtypeVersion();

        // TODO: meta resolver
        $meta = array();//$this->_db->fetchCol($selectMeta);
        sort($meta);

        $rii = new \RecursiveIteratorIterator($elementStructure->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);
        $content = array();
        foreach ($rii as $structure) {
            foreach ($structure->getValues() as $value) {
                $content[$structure->getId() . '__' . $value->getId()] = $value->getValue();
            }
        }

        $values = array(
            'eid'                => $eid,
            'elementtypeId'      => $elementtypeId,
            'elementtypeVersion' => $elementtypeVersion,
            'meta'               => $meta,
            'content'            => $content,
        );

        return $values;
    }

    /**
     * @param array $values
     *
     * @return string
     */
    private function hashValues(array $values)
    {
        return hash($this->algo, serialize($values));
    }
}
