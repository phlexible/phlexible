<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaTemplate\Previewer;

use Monolog\Handler\TestHandler;
use Phlexible\Component\MediaTemplate\Applier\VideoTemplateApplier;
use Phlexible\Component\MediaTemplate\Model\VideoTemplate;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Video previewer
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class VideoPreviewer implements PreviewerInterface
{
    /**
     * @var VideoTemplateApplier
     */
    private $applier;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @param VideoTemplateApplier $applier
     * @param string               $cacheDir
     */
    public function __construct(VideoTemplateApplier $applier, $cacheDir)
    {
        $this->applier = $applier;
        $this->cacheDir = $cacheDir;
    }

    /**
     * {@inheritdoc}
     */
    public function create($filePath, array $params)
    {
        $filesystem = new Filesystem();
        if (!$filesystem->exists($this->cacheDir)) {
            $filesystem->mkdir($this->cacheDir);
        }

        $template = new VideoTemplate();
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

        if ($debug) {
            $logger = $this->applier->getLogger();
            $logger->pushHandler(new TestHandler());
        }

        $extension = $this->applier->getExtension($template);
        $cacheFilename = $this->cacheDir . 'preview_video.' . $extension;
        $video = $this->applier->apply($template, $filePath, $cacheFilename);

        if ($debug) {
            /* @var $logger \Monolog\Logger */
            $handler = $this->applier->getLogger()->popHandler();

            $debug = '';
            foreach ($handler->getRecords() as $record) {
                $debug .= $record['message'] . PHP_EOL;
            }

            //$debug .= print_r($video->getStreams()->videos()->first(), 1);
        } else {
            $debug = '';
        }

        $videoStream = $video->getStreams()->videos()->first();
        $data = [
            'file'     => basename($cacheFilename),
            'size'     => filesize($cacheFilename),
            'template' => $templateKey,
            'width'    => $videoStream->get('width'),
            'height'   => $videoStream->get('height'),
            'format'   => $extension,
            'mimetype' => $this->applier->getMimetype($template),
            'debug'    => $debug
        ];

        return $data;
    }
}
