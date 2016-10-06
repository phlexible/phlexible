<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\CmsBundle\DataCollector;

use Phlexible\Bundle\ElementBundle\ContentElement\ContentElementLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\DataCollector\LateDataCollectorInterface;

/**
 * Content data collector
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ContentDataCollector extends DataCollector implements LateDataCollectorInterface
{
    /**
     * @var ContentElementLoader
     */
    private $elementLoader;

    /**
     * @param null $elementLoader
     */
    public function __construct($elementLoader = null)
    {
        if (null !== $elementLoader && $elementLoader instanceof ContentElementLoader) {
            $this->elementLoader = $elementLoader;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        // everything is done as late as possible
    }

    /**
     * {@inheritdoc}
     */
    public function lateCollect()
    {
        if (null !== $this->elementLoader) {
            $this->data['elements'] = array();
            foreach ($this->elementLoader->getElements() as $element) {
                $this->data['elements'][] = array(
                    'eid' => $element->getEid(),
                    'version' => $element->getVersion(),
                    'language' => $element->getLanguage(),
                );
            }
        }
    }

    /**
     * Gets the called events.
     *
     * @return int
     */
    public function countElements()
    {
        return isset($this->data['elements']) ? count($this->data['elements']) : 0;
    }

    /**
     * Gets the called events.
     *
     * @return int
     */
    public function getElements()
    {
        return isset($this->data['elements']) ? $this->data['elements'] : array();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'cms';
    }
}
