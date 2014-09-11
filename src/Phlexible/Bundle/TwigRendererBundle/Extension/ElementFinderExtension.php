<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TwigRendererBundle\Extension;

use Phlexible\Bundle\ElementBundle\Model\ElementStructureValue;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\ElementFinder;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\ElementFinderResultPool;
use Phlexible\Bundle\ElementFinderBundle\Entity\ElementFinderConfig;
use Phlexible\Bundle\MediaTemplateBundle\Model\TemplateManagerInterface;

/**
 * Twig element finder extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementFinderExtension extends \Twig_Extension
{
    /**
     * @var ElementFinder
     */
    private $elementFinder;

    /**
     * @param ElementFinder $elementFinder
     */
    public function __construct(ElementFinder $elementFinder)
    {
        $this->elementFinder = $elementFinder;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('finder', array($this, 'finder')),
        );
    }

    /**
     * @param ElementStructureValue|array $field
     *
     * @return ElementFinderResultPool
     */
    public function finder($field)
    {
        if (is_array($field)) {
            $values = $field;
        } elseif ($field instanceof ElementStructureValue) {
            $values = $field->getValue();
        } else {
            return '';
        }

        $elementFinderConfig = new ElementFinderConfig();
        $elementFinderConfig
            ->setElementtypeIds(explode(',', $values['elementtypeIds']))
            ->setNavigation($values['inNavigation'])
            ->setMaxDepth($values['maxDepth'])
            ->setFilter($values['filter'])
            ->setSortField($values['sortField'])
            ->setSortOrder($values['sortDir'])
            ->setTreeId($values['startTreeId']);

        $resultPool = $this->elementFinder->find($elementFinderConfig, array('de'), true);

        return $resultPool;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'phlexible_element_finder';
    }
}