<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\CmsBundle\DataCollector;

use Phlexible\Bundle\ElementBundle\ContentElement\ContentElementLoader;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeInterface;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeManagerInterface;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeNode;
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
     * @var ContentTreeManagerInterface
     */
    private $treeManager;

    /**
     * @var ContentElementLoader
     */
    private $elementLoader;

    /**
     * @param null $treeManager
     * @param null $elementLoader
     */
    public function __construct($treeManager = null, $elementLoader = null)
    {
        if (null !== $treeManager && $treeManager instanceof ContentTreeManagerInterface) {
            $this->treeManager = $treeManager;
        }

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
        if (null !== $this->treeManager) {
            $this->data['nodes'] = array();
            foreach ($this->treeManager->findAll() as $tree) {
                /* @var $tree ContentTreeInterface */
                foreach ($tree->getNodes() as $node) {
                    /* @var $node ContentTreeNode */
                    $path = array();
                    foreach ($node->getTree()->getPath($node) as $pathNode) {
                        $path[$pathNode->getId()] = $pathNode->getTitle();
                    }
                    $this->data['nodes'][] = array(
                        'id' => $node->getId(),
                        'title' => $node->getTitle(),
                        'path' => $path,
                    );
                }
            }
        }
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
     * Gets the number of loaded nodes.
     *
     * @return int
     */
    public function countNodes()
    {
        return isset($this->data['nodes']) ? count($this->data['nodes']) : 0;
    }

    /**
     * Gets the number of loaded elements.
     *
     * @return int
     */
    public function countElements()
    {
        return isset($this->data['elements']) ? count($this->data['elements']) : 0;
    }

    /**
     * Gets the loaded nodes.
     *
     * @return array
     */
    public function getNodes()
    {
        return isset($this->data['nodes']) ? $this->data['nodes'] : array();
    }

    /**
     * Gets the loaded elements.
     *
     * @return array
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
