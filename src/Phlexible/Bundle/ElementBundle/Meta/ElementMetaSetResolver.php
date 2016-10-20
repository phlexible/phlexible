<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Meta;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Component\MetaSet\Model\MetaSet;
use Phlexible\Component\MetaSet\Model\MetaSetManagerInterface;

/**
 * Element meta set resolver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementMetaSetResolver
{
    /**
     * @var MetaSetManagerInterface
     */
    private $metaSetManager;

    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @param MetaSetManagerInterface $metaSetManager
     * @param ElementService          $elementService
     */
    public function __construct(MetaSetManagerInterface $metaSetManager, ElementService $elementService)
    {
        $this->metaSetManager = $metaSetManager;
        $this->elementService = $elementService;
    }

    /**
     * @param ElementVersion $elementVersion
     *
     * @return MetaSet
     */
    public function resolve(ElementVersion $elementVersion)
    {
        $elementtype = $this->elementService->findElementtype($elementVersion->getElement());

        if (!$elementtype->getMetaSetId()) {
            return null;
        }

        $metaSet = $this->metaSetManager->find($elementtype->getMetaSetId());

        return $metaSet;
    }
}
