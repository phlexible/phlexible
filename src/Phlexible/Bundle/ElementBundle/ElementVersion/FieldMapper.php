<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ElementVersion;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\ElementBundle\Model\ElementStructure;
use Phlexible\Bundle\ElementBundle\Model\ElementStructureValue;

/**
 * Field mapper
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class FieldMapper
{
    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var FieldMapperInterface[]
     */
    private $mappers = array();

    /**
     * @param ElementService         $elementService
     * @param FieldMapperInterface[] $mappers
     */
    public function __construct(ElementService $elementService, array $mappers = array())
    {
        $this->elementService = $elementService;
        $this->mappers = $mappers;
    }

    /**
     * @param FieldMapperInterface $mapper
     *
     * @return $this
     */
    public function addMapper(FieldMapperInterface $mapper)
    {
        $this->mappers[] = $mapper;

        return $this;
    }

    /**
     * @param ElementVersion $elementVersion
     * @param string         $language
     *
     * @return array
     */
    public function map(ElementVersion $elementVersion, $language)
    {
        $elementtypeVersion = $this->elementService->findElementtypeVersion($elementVersion);
        $elementStructure = $this->elementService->findElementStructure($elementVersion, $language);
        $mappings = $elementtypeVersion->getMappings();

        $titles = array();
        foreach ($mappings as $key => $mapping) {
            if ($mapper = $this->findFieldMapper($key)) {
                $title = $mapper->map($elementStructure, $mapping);
                if ($title) {
                    $titles[$key] = $title;
                }
            }
        }

        if (empty($titles['backend'])) {
            $elementtype = $elementtypeVersion->getElementtype();
            $titles['backend'] = '[' . $elementtype->getTitle() . ', ' . $language . ']';
        }

        return $titles;
    }

    /**
     * @param string $key
     *
     * @return FieldMapperInterface|null
     */
    private function findFieldMapper($key)
    {
        foreach ($this->mappers as $mapper) {
            if ($mapper->accept($key)) {
                return $mapper;
            }
        }

        return null;
    }
}