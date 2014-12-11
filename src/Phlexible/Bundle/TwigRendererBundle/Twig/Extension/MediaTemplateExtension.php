<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TwigRendererBundle\Twig\Extension;

use Phlexible\Component\MediaTemplate\Model\TemplateManagerInterface;

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
        return [
            new \Twig_SimpleFunction('mediatemplate', [$this, 'mediatemplate']),
        ];
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
            return [];
        }

        return $template->getParameters();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'phlexible_media_template';
    }
}
