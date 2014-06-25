<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementRendererBundle\VersionStrategy;

use Phlexible\Bundle\ElementBundle\Element\Element;
use Phlexible\Bundle\ElementBundle\ElementService;
use Symfony\Component\HttpFoundation\Request;

/**
 * Latest version strategy
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LatestVersionStrategy implements VersionStrategyInterface
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
    public function getName()
    {
        return 'latest';
    }

    /**
     * {@inheritdoc}
     */
    public function findLanguage(Request $request, Element $element, array $languages)
    {
        // Before Init Version Event
        /*
        $beforeEvent = new \Makeweb_Renderers_Event_BeforeInitElementVersion($this, $treeNode, $element);
        if (false === $this->dispatcher->dispatch($beforeEvent))
        {
            return;
        }
        */

        $availableLanguages = $request->attributes->get('availableLanguages', array('de'));

        $elementVersion = $this->elementService->findLatestElementVersion($element);

        foreach ($availableLanguages as $language) {
            $this->logger->notice('Trying language ' . $language);

            $elementLanguage = $language;
            break;

            // only if element has been saved at least once
            $firstVersion = \Makeweb_Elements_History::getFirstVersionByEidAndLanguage(
                                                     $element->getEid(),
                                                         $language
            );

            if ((null !== $firstVersion) && ($firstVersion <= $version)) {
                $elementLanguage = $language;
                break;
            }
        }

        $this->logger->notice('Using language ' . $elementLanguage);

        // Init Version Event
        /*
        $event = new \Makeweb_Renderers_Event_InitElementVersion($this, $treeNode, $element);
        $this->dispatcher->dispatch($event);
        */

        return array($elementVersion, $elementLanguage);
    }

    /**
     * {@inheritdoc}
     */
    public function findVersion(Request $request, Element $element, $language)
    {
        // Before Init Version Event
        /*
        $beforeEvent = new \Makeweb_Renderers_Event_BeforeInitElementVersion($this, $treeNode, $element);
        if (false === $this->dispatcher->dispatch($beforeEvent))
        {
            return;
        }
        */

        $availableLanguages = $request->attributes->get('availableLanguages', array('de'));

        $elementVersion = $this->elementService->findLatestElementVersion($element);

        foreach ($availableLanguages as $language) {
            $this->logger->notice('Trying language ' . $language);

            $elementLanguage = $language;
            break;

            // only if element has been saved at least once
            $firstVersion = \Makeweb_Elements_History::getFirstVersionByEidAndLanguage(
                                                     $element->getEid(),
                                                         $language
            );

            if ((null !== $firstVersion) && ($firstVersion <= $version)) {
                $elementLanguage = $language;
                break;
            }
        }

        $this->logger->notice('Using language ' . $elementLanguage);

        // Init Version Event
        /*
        $event = new \Makeweb_Renderers_Event_InitElementVersion($this, $treeNode, $element);
        $this->dispatcher->dispatch($event);
        */

        return array($elementVersion, $elementLanguage);
    }
}
