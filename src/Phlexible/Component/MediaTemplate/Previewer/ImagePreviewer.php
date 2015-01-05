<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaTemplate\Previewer;

use Phlexible\Bundle\MediaManagerBundle\Entity\File;
use Phlexible\Component\MediaTemplate\Applier\ImageTemplateApplier;
use Phlexible\Component\MediaTemplate\Model\ImageTemplate;
use Symfony\Component\Filesystem\Filesystem;

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
     * @var string
     */
    private $cacheDir;

    /**
     * @param ImageTemplateApplier $applier
     * @param string               $cacheDir
     */
    public function __construct(ImageTemplateApplier $applier, $cacheDir)
    {
        $this->applier = $applier;
        $this->cacheDir = $cacheDir;
    }

    /**
     * {@inheritdoc}
     */
    public function create($filePath,  array $params)
    {
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
