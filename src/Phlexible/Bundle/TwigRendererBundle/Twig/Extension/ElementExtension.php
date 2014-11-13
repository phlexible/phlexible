<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TwigRendererBundle\Twig\Extension;

use Phlexible\Bundle\ElementBundle\ContentElement\ContentElement;
use Phlexible\Bundle\ElementBundle\ContentElement\ContentElementLoader;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeContext;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Twig element extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementExtension extends \Twig_Extension
{
    /**
     * @var ContentElementLoader
     */
    private $contentElementLoader;

    /**
     * @param ContentElementLoader $contentElementLoader
     */
    public function __construct(ContentElementLoader $contentElementLoader)
    {
        $this->contentElementLoader = $contentElementLoader;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('element', array($this, 'element')),
        );
    }

    /**
     * @param TreeNodeInterface|ContentTreeContext|int $eid
     *
     * @return ContentElement|null
     */
    public function element($eid)
    {
        if (is_int($eid)) {
            $language = 'de';
            $version = 1;
        } elseif ($eid instanceof TreeNodeInterface) {
            $node = $eid;
            $eid = $node->getTypeId();
            $language = 'de';
            $version = $node->getTree()->getVersion($node, $language);
        } elseif ($eid instanceof ContentTreeContext) {
            $node = $eid->getNode();
            $eid = $node->getTypeId();
            $language = 'de';
            $version = $node->getTree()->getVersion($node, $language);
        } else {
            return null;
        }

        return $this->contentElementLoader->load($eid, $version, $language);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'phlexible_element';
    }
}
