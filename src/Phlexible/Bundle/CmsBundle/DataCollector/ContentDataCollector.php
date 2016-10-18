<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\CmsBundle\DataCollector;

use Phlexible\Bundle\ElementBundle\ContentElement\ContentElementLoader;
use Phlexible\Bundle\TeaserBundle\ContentTeaser\DelegatingContentTeaserManager;
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
     * @var DelegatingContentTeaserManager
     */
    private $teaserManager;

    /**
     * @var ContentElementLoader
     */
    private $elementLoader;

    /**
     * @param null $treeManager
     * @param null $teaserManager
     * @param null $elementLoader
     */
    public function __construct($treeManager = null, $teaserManager = null, $elementLoader = null)
    {
        if (null !== $treeManager && $treeManager instanceof ContentTreeManagerInterface) {
            $this->treeManager = $treeManager;
        }

        if (null !== $teaserManager && $teaserManager instanceof DelegatingContentTeaserManager) {
            $this->teaserManager = $teaserManager;
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
                        'type' => $node->getType(),
                        'typeId' => $node->getTypeId(),
                        'title' => $node->getTitle(),
                        'path' => $path,
                    );
                }
            }
        }
        if (null !== $this->teaserManager) {
            $this->data['teasers'] = array();
            foreach ($this->teaserManager->getTeasers() as $teaser) {
                $this->data['teasers'][] = array(
                    'id' => $teaser->getId(),
                    'type' => $teaser->getType(),
                    'typeId' => $teaser->getTypeId(),
                    'title' => $teaser->getTitle(),
                    'nodeId' => $teaser->getTreeId(),
                );
            }
        }
        if (null !== $this->elementLoader) {
            $this->data['elements'] = array();
            foreach ($this->elementLoader->getElements() as $element) {
                $this->data['elements'][] = array(
                    'eid' => $element->getEid(),
                    'version' => $element->getVersion(),
                    'language' => $element->getLanguage(),
                    'elementtypeId' => $element->getElementtypeId(),
                    'elementtypeType' => $element->getElementtypeType(),
                    'elementtypeUniqueId' => $element->getElementtypeUniqueId(),
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
     * Gets the number of loaded teasers.
     *
     * @return int
     */
    public function countTeasers()
    {
        return isset($this->data['teasers']) ? count($this->data['teasers']) : 0;
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
     * Gets the loaded teasers.
     *
     * @return array
     */
    public function getTeasers()
    {
        return isset($this->data['teasers']) ? $this->data['teasers'] : array();
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
