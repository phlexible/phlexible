<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\Twig\Extension;

use Phlexible\Bundle\ElementBundle\Model\ElementStructureValue;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\ElementFinder;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\ElementFinderResultPool;
use Phlexible\Bundle\ElementFinderBundle\Entity\ElementFinderConfig;
use Symfony\Component\HttpFoundation\RequestStack;

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
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param ElementFinder $elementFinder
     * @param RequestStack  $requestStack
     */
    public function __construct(ElementFinder $elementFinder, RequestStack $requestStack)
    {
        $this->elementFinder = $elementFinder;
        $this->requestStack = $requestStack;
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

        $elementtypeIds = !empty($values['elementtypeIds']) ? explode(',', $values['elementtypeIds']) : array();
        $inNavigation = !empty($values['inNavigation']);
        $maxDepth = strlen($values['maxDepth']) ? (int) $values['maxDepth'] : null;
        $filter = !empty($values['filter']) ? $values['filter'] : null;
        $sortField = !empty($values['sortField']) ? $values['sortField'] : null;
        $sortDir = !empty($values['sortDir']) ? $values['sortDir'] : null;
        $startTreeId = !empty($values['startTreeId']) ? $values['startTreeId'] : null;

        $elementFinderConfig = new ElementFinderConfig();
        $elementFinderConfig
            ->setElementtypeIds($elementtypeIds)
            ->setNavigation($inNavigation)
            ->setMaxDepth($maxDepth)
            ->setFilter($filter)
            ->setSortField($sortField)
            ->setSortOrder($sortDir)
            ->setTreeId($startTreeId);

        $resultPool = $this->elementFinder->find(
            $elementFinderConfig,
            array('de'),
            $this->requestStack->getCurrentRequest()->attributes->get('preview', false)
        );

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