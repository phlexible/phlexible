<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ContentElement;

use Phlexible\Bundle\AccessControlBundle\Rights as ContentRightsManager;
use Phlexible\Bundle\ElementBundle\ElementService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Content element builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ContentElementBuilder
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param LoggerInterface          $logger
     * @param ElementService           $elementService
     */
    public function __construct(EventDispatcherInterface $dispatcher,
                                LoggerInterface $logger,
                                ElementService $elementService)
    {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
        $this->elementService = $elementService;
    }

    /**
     * {@inheritdoc}
     */
    public function build($eid, $version, $language)
    {
        $element            = $this->elementService->findElement($eid);
        $elementVersion     = $this->elementService->findElementVersion($element, $version);
        $elementStructure   = $this->elementService->findElementStructure($elementVersion, $language);
        $elementtype        = $this->elementService->findElementtype($element);

        $mappedFields = $elementVersion->getMappedFields();
        if (isset($mappedFields[$language])) {
            $mappedFields = $mappedFields[$language];
        } else {
            $mappedFields = array();
        }
        $contentElement = new ContentElement(
            $element->getEid(),
            $element->getUniqueId(),
            $elementtype->getId(),
            $elementtype->getUniqueId(),
            $elementtype->getType(),
            $elementtype->getTemplate(),
            $elementVersion->getVersion(),
            $language,
            $mappedFields,
            $elementStructure
        );

        return $contentElement;
    }
}
