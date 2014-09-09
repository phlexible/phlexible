<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TwigRendererBundle\Extension;

use Phlexible\Bundle\MediaTemplateBundle\Model\TemplateManagerInterface;

/**
 * Twig media template extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaTemplateExtension extends \Twig_Extension
{
    /**
     * @var TemplateManagerInterface
     */
    private $templateManager;

    /**
     * @param TemplateManagerInterface $templateManager
     */
    public function __construct(TemplateManagerInterface $templateManager)
    {
        $this->templateManager = $templateManager;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('mediatemplate', array($this, 'mediatemplate')),
        );
    }

    /**
     * @param string $id
     *
     * @return array
     */
    public function mediatemplate($id)
    {
        $template = $this->templateManager->find($id);

        if (!$template) {
            return array();
        }

        return $template->getParameters();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mediatemplate';
    }
}