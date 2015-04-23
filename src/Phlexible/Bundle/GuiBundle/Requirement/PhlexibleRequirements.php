<?php

namespace Phlexible\Bundle\GuiBundle\Requirement;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Process\ProcessBuilder;

/**
 * This class specifies all requirements and optional recommendations that
 * are necessary to run the Symfony Standard Edition.
 *
 * @author Tobias Schultze <http://tobion.de>
 * @author Fabien Potencier <fabien@symfony.com>
 */
class PhlexibleRequirements extends RequirementCollection
{
    /**
     * Constructor that initializes the requirements.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->addPhpIniRecommendation(
            'post_max_size',
            function() {
                $size = ini_get('post_max_size');
                $size = trim($size);
                $unit = strtolower(substr($size, -1, 1));
                switch ($unit) {
                    case 'g':
                        $size = $size * 1024 * 1024 * 1024;
                    case 'm':
                        $size = $size * 1024 * 1024;
                    case 'k':
                        $size = $size * 1024;
                    default:
                        $size = (int) $size;
                }

                return $size > 100000000;
            },
            false,
            'post_max_size should be set to a value > 100m',
            'Set post_max_size to at least 100m in php.ini'
        );

        $this->addPhpIniRecommendation(
            'upload_max_filesize',
            function() {
                $size = ini_get('upload_max_filesize');
                $size = trim($size);
                $unit = strtolower(substr($size, -1, 1));
                switch ($unit) {
                    case 'g':
                        $size = $size * 1024 * 1024 * 1024;
                    case 'm':
                        $size = $size * 1024 * 1024;
                    case 'k':
                        $size = $size * 1024;
                    default:
                        $size = (int) $size;
                }

                return $size > 100000000;
            },
            false,
            'upload_max_filesize should be set to a value > 100m',
            'Set upload_max_filesize to at least 100m in php.ini'
        );

        try {
            $container->get('phlexible_media_tool.ffprobe')->getFFProbeDriver()->command('-version');
            $valid = true;
        } catch (\Exception $e) {
            $valid = false;
        }
        $this->addRequirement($valid, 'Path to ffprobe executable has to be configured.', 'Set ffprobe path.');

        try {
            $container->get('phlexible_media_tool.ffmpeg')->getFFMpegDriver()->command('-version');
            $valid = true;
        } catch (\Exception $e) {
            $valid = false;
        }
        $this->addRequirement($valid, 'Path to ffmpeg executable has to be configured.', 'Set ffmpeg path.');

        try {
            $container->get('phlexible_media_tool.poppler.pdfinfo')->command('-v');
            $valid = true;
        } catch (\Exception $e) {
            $valid = false;
        }
        $this->addRequirement($valid, 'Path to pdfinfo executable has to be configured.', 'Set pdfinfo path.');

        try {
            $container->get('phlexible_media_tool.poppler.pdftotext')->command('-v');
            $valid = true;
        } catch (\Exception $e) {
            $valid = false;
        }
        $this->addRequirement($valid, 'Path to pdftotext executable has to be configured.', 'Set pdftotext path.');
    }
}
