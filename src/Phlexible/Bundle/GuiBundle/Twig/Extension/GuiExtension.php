<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Twig\Extension;

/**
 * Twig gui extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GuiExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    /**
     * @var string
     */
    private $projectTitle;

    /**
     * @var string
     */
    private $projectVersion;

    /**
     * @var string
     */
    private $projectUrl;

    /**
     * @param string $projectTitle
     * @param string $projectVersion
     * @param string $projectUrl
     */
    public function __construct($projectTitle, $projectVersion, $projectUrl)
    {
        $this->projectTitle = $projectTitle;
        $this->projectVersion = $projectVersion;
        $this->projectUrl = $projectUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobals()
    {
        return [
            'project' => [
                'title'   => $this->projectTitle,
                'version' => $this->projectVersion,
                'url'     => $this->projectUrl,
            ],
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gui_extension';
    }
}
