<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\Preview;

use Monolog\Handler\TestHandler;
use Phlexible\Bundle\MediaSiteBundle\Model\File;
use Phlexible\Bundle\MediaTemplateBundle\Applier\ImageTemplateApplier;
use Phlexible\Bundle\MediaTemplateBundle\Model\ImageTemplate;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Image preview
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ImagePreview implements PreviewerInterface
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
        $assetPath = dirname(__DIR__) . '/Resources/public/images/';
        $previewImage = 'test_' . $params['preview_image'];
        unset($params['preview_image']);

        if (file_exists($assetPath . $previewImage . '.png')) {
            $previewImage .= '.png';
        } else {
            $previewImage .= '.jpg';
        }

        $filePath = $assetPath . $previewImage;

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

        if ($debug) {
            $logger = $this->applier->getLogger();
            $logger->pushHandler(new TestHandler());
        }

        $extension = $this->applier->getExtension($template);
        $cacheFilename = $this->cacheDir . 'preview_image.' . $extension;
        $image = $this->applier->apply($template, new File(), $filePath, $cacheFilename);

        if ($debug) {
            /* @var $logger \Monolog\Logger */
            $handler = $this->applier->getLogger()->popHandler();

            $debug = '';
            foreach ($handler->getRecords() as $record) {
                $debug .= $record['message'] . PHP_EOL;
            }
        } else {
            $debug = '';
        }

        $data = array(
            'file'     => basename($cacheFilename),
            'size'     => filesize($cacheFilename),
            'template' => $templateKey,
            'width'    => $image->getWidth(),
            'height'   => $image->getHeight(),
            'format'   => $extension,
            'mimetype' => $this->applier->getMimetype($template),
            'debug'    => $debug,
        );

        return $data;
    }
}