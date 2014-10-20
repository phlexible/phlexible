<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Twig\Extension;

use Symfony\Component\Routing\RouterInterface;

/**
 * Twig gui extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GuiExtension extends \Twig_Extension
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