<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\Mediator;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Entity\Element;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\ElementFinderBundle\Entity\ElementFinderConfig;
use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\ElementFinderBundle\Model\ElementFinderManagerInterface;
use Phlexible\Bundle\TeaserBundle\Mediator\MediatorInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;

/**
 * Catch mediator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CatchMediator implements MediatorInterface
{
    /**
     * @var ElementFinderManagerInterface
     */
    private $catchManager;

    /**
     * @param ElementFinderManagerInterface $catchManager
     */
    public function __construct(ElementFinderManagerInterface $catchManager)
    {
        $this->catchManager = $catchManager;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(Teaser $teaser)
    {
        return $teaser->getType() === 'catch';
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(Teaser $teaser, $field, $language)
    {
        $catch = $this->getObject($teaser);

        return $catch->getTitle();
    }

    /**
     * {@inheritdoc}
     */
    public function getUniqueId(Teaser $teaser)
    {
        return 'catch_' . $this->getObject($teaser)->getId();
    }

    /**
     * {@inheritdoc}
     *
     * @return ElementFinderConfig
     */
    public function getObject(Teaser $teaser)
    {
        return $this->catchManager->findCatch($teaser->getTypeId());
    }

    /**
     * {@inheritdoc}
     *
     * @return ElementFinderConfig
     */
    public function getVersionedObject(Teaser $teaser)
    {
        return $this->catchManager->findCatch($teaser->getTypeId());
    }
}
