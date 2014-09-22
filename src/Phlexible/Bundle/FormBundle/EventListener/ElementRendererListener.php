<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FormBundle\EventListener;

use Phlexible\Bundle\ElementBundle\ContentElement\ContentElement;
use Phlexible\Bundle\ElementRendererBundle\Configurator\RenderConfiguration;
use Phlexible\Bundle\ElementRendererBundle\ElementRendererEvents;
use Phlexible\Bundle\ElementRendererBundle\Event\ConfigureEvent;
use Phlexible\Bundle\FormBundle\FormHandler\FormHandlerCollection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Element renderer listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementRendererListener implements EventSubscriberInterface
{
    /**
     * @var FormHandlerCollection
     */
    private $forms;

    /**
     * @param FormHandlerCollection $forms
     */
    public function __construct(FormHandlerCollection $forms)
    {
        $this->forms = $forms;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            ElementRendererEvents::CONFIGURE_ELEMENT => 'onConfigureElement',
        );
    }

    /**
     * @param ConfigureEvent $event
     */
    public function onConfigureElement(ConfigureEvent $event)
    {
        $configuration = $event->getConfiguration();
        $contentElement = $configuration->get('contentElement');
        /* @var $contentElement ContentElement */

        $rootStructure = $contentElement->getStructure();
        foreach ($rootStructure->getValues() as $value) {
            if ($value->getType() === 'form') {
                $this->processForm($value->getValue(), $configuration);
            }
        }

        $rii = new \RecursiveIteratorIterator($rootStructure->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($rii as $structure) {
            foreach ($structure->getValues() as $value) {
                if ($value->getType() === 'form') {
                    $this->processForm($value->getValue(), $configuration);
                }
            }
        }
    }

    /**
     * @param string              $formName
     * @param RenderConfiguration $configuration
     */
    private function processForm($formName, RenderConfiguration $configuration)
    {
        $formHandler = $this->forms->get($formName);
        $form = $formHandler->createForm();

        $formHandler->handleRequest($configuration->get('request'));

        $configuration
            ->addFeature('form')
            ->set('forms', array($formName => $form))
            ->set('formViews', array($formName => $form->createView()));
    }
}
