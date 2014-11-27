<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\Previewer;

use Monolog\Handler\TestHandler;
use Phlexible\Bundle\MediaSiteBundle\Model\File;
use Phlexible\Bundle\MediaTemplateBundle\Applier\ImageTemplateApplier;
use Phlexible\Bundle\MediaTemplateBundle\Model\ImageTemplate;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * Image previewer
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ImagePreviewer implements PreviewerInterface
{
    /**
     * @var ImageTemplateApplier
     */
    private $applier;

    /**
     * @var FileLocator
     */
    private $locator;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @param ImageTemplateApplier $applier
     * @param FileLocator          $locator
     * @param string               $cacheDir
     */
    public function __construct(ImageTemplateApplier $applier, FileLocator $locator, $cacheDir)
    {
        $this->applier = $applier;
        $this->locator = $locator;
        $this->cacheDir = $cacheDir;
    }

    /**
     * @return string
     */
    public function getPreviewDir()
    {
        return $this->cacheDir;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function create(array $params)
    {
        $filePrefix = "@PhlexibleMediaTemplateBundle/Resources/public/images/test_{$params['preview_image']}";
        unset($params['preview_image']);

        $filePath = $this->locator->locate("$filePrefix.jpg", null, true);

        $filesystem = new Filesystem();
        if (!$filesystem->exists($this->cacheDir)) {
            $filesystem->mkdir($this->cacheDir);
        }

        $template = new ImageTemplate();
        $templateKey = 'unknown';
        $debug = false;
        foreach ($params as $key => $value) {
            if ($key === 'xmethod') {
                $key = 'method';
            } elseif ($key === 'backgroundcolor' && !preg_match('/^\#[0-9A-Za-z]{6}$/', $value)) {
                $value = '';
            } elseif ($key === 'template') {
                $templateKey = $value;
                continue;
            } elseif ($key === '_dc') {
                continue;
            } elseif ($key === 'debug') {
                $debug = true;
                continue;
            }

            $template->setParameter($key, $value);
        }
        //$template->setNoCache();

        $extension = $this->applier->getExtension($template);
        $cacheFilename = $this->cacheDir . 'preview_image.' . $extension;
        $image = $this->applier->apply($template, new File(), $filePath, $cacheFilename);

        $debug = json_encode($template->getParameters());

        $data = [
            'file'     => basename($cacheFilename),
            'size'     => filesize($cacheFilename),
            'template' => $templateKey,
            'width'    => $image->getSize()->getWidth(),
            'height'   => $image->getSize()->getHeight(),
            'format'   => $extension,
            'mimetype' => $this->applier->getMimetype($template),
            'debug'    => $debug,
        ];

        return $data;
    }
}
