<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaTemplateBundle\Twig\Extension;

use Phlexible\Component\MediaTemplate\Model\TemplateManagerInterface;

/**
 * Twig media template extension.
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
