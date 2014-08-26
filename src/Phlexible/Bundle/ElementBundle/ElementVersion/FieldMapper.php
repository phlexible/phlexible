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
use Phlexible\Bundle\ElementBundle\Entity\ElementVersionMappedField;
use Phlexible\Bundle\ElementBundle\Model\ElementStructure;

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
     * @param ElementVersion   $elementVersion
     * @param ElementStructure $elementStructure
     * @param string           $language
     *
     * @return array
     */
    public function extract(ElementVersion $elementVersion, ElementStructure $elementStructure, $language)
    {
        $elementtypeVersion = $this->elementService->findElementtypeVersion($elementVersion);
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
     * @param ElementVersion   $elementVersion
     * @param ElementStructure $elementStructure
     * @param string           $language
     */
    public function apply(ElementVersion $elementVersion, ElementStructure $elementStructure, $language)
    {
        $mapping = $this->extract($elementVersion, $elementStructure, $language);

        $mappedFields = $elementVersion->getMappedFields();
        if (!$mappedFields->contains($language)) {
            $mappedField = new ElementVersionMappedField();
            $mappedField
                ->setLanguage($language)
                ->setElementVersion($elementVersion);
            $mappedFields->set($language, $mappedField);
        }

        $mappedField = $mappedFields->get($language);
        $mappedField->setMapping($mapping);

        $elementVersion->setMappedFields($mappedFields);
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