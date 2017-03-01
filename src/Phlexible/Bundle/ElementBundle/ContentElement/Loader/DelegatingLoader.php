<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\ContentElement\Loader;

use Phlexible\Bundle\ElementBundle\ContentElement\ContentElement;
use Phlexible\Bundle\ElementBundle\ElementService;

/**
 * Delegating loader.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DelegatingLoader implements LoaderInterface
{
    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @param ElementService $elementService
     */
    public function __construct(ElementService $elementService)
    {
        $this->elementService = $elementService;
    }

    /**
     * {@inheritdoc}
     */
    public function load($eid, $version, $language)
    {
        $element = $this->elementService->findElement($eid);
        if ($version === -1) {
            $elementVersion = $this->elementService->findLatestElementVersion($element);
        } else {
            $elementVersion = $this->elementService->findElementVersion($element, $version);
        }

        if (!$elementVersion) {
            return null;
        }

        $elementtype = $this->elementService->findElementtype($element);

        $eid = $element->getEid();
        $uniqueId = $element->getUniqueId();
        $elementtypeId = $elementtype->getId();
        $elementtypeUniqueId = $elementtype->getUniqueId();
        $elementtypeType = $elementtype->getType();
        $elementtypeTemplate = $elementtype->getTemplate();

        $mappedFields = $elementVersion->getMappedFields()[$language];

        $structure = $this->elementService->findElementStructure($elementVersion, $language);

        $contentElement = new ContentElement(
            $eid,
            $uniqueId,
            $elementtypeId,
            $elementtypeUniqueId,
            $elementtypeType,
            $elementtypeTemplate,
            $version,
            $language,
            $mappedFields,
            $structure
        );

        return $contentElement;
    }
}
