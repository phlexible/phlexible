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
 * Online version strategy
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class OnlineVersionStrategy implements VersionStrategyInterface
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
        return 'online';
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

        $elementLanguage = $this->elementService->findOnlineLanguage($element, $languages);

        // Init Version Event
        /*
        $event = new \Makeweb_Renderers_Event_InitElementVersion($this, $treeNode, $element);
        $this->dispatcher->dispatch($event);
        */

        return $elementLanguage;
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

        $elementVersion = $this->elementService->findOnlineElementVersion($element, $language);

        // Init Version Event
        /*
        $event = new \Makeweb_Renderers_Event_InitElementVersion($this, $treeNode, $element);
        $this->dispatcher->dispatch($event);
        */

        return $elementVersion;
    }
}
