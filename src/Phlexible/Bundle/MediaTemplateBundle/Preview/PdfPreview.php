<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\Preview;

use Monolog\Handler\TestHandler;
use Phlexible\Bundle\MediaTemplateBundle\Applier\PdfTemplateApplier;
use Phlexible\Bundle\MediaTemplateBundle\Model\PdfTemplate;
use Symfony\Component\Filesystem\Filesystem;

/**
 * PDF preview
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PdfPreview implements PreviewerInterface
{
    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var PdfTemplateApplier
     */
    private $applier;

    /**
     * @param PdfTemplateApplier $applier
     * @param string             $cacheDir
     */
    public function __construct(PdfTemplateApplier $applier, $cacheDir)
    {
        $this->cacheDir = $cacheDir;
        $this->applier = $applier;
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
        $assetPath = dirname(__DIR__) . '/Resources/public/pdf/';
        $previewPdf = 'test.pdf';
        $filePath = $assetPath . $previewPdf;

        $filesystem = new Filesystem();
        if (!$filesystem->exists($this->cacheDir)) {
            $filesystem->mkdir($this->cacheDir);
        }

        $template = new PdfTemplate();
        $templateKey = 'unknown';
        $debug = false;
        foreach ($params as $key => $value) {
            if ($key === 'template') {
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

        $viewer = dirname(dirname(__FILE__)) . '/Resources/public/swf/SimpleViewer.swf';
        $template->setParameter('viewer', $viewer);

        if ($debug) {
            $logger = $this->applier->getLogger();
            $logger->pushHandler(new TestHandler());
        }

        $extension = $this->applier->getExtension($template);
        $cacheFilename = $this->cacheDir . 'preview_pdf.' . $extension;
        $this->applier->apply($template, $filePath, $cacheFilename);

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

        $data = [
            'file' => basename($cacheFilename),
            'size' => filesize($cacheFilename),
            'template' => $templateKey,
            'format' => $extension,
            'mimetype' => $this->applier->getMimetype($template),
            'debug' => $debug,
        ];

        return $data;
    }
}
