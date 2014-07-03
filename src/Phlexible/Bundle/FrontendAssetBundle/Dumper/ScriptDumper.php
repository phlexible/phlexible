<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendAssetBundle\Dumper;

use Phlexible\Bundle\FrontendAssetBundle\Collector\Block;
use Phlexible\CoreComponent\Compressor\JavascriptCompressor\JavascriptCompressorInterface;
use Psr\Log\LoggerInterface;

/**
 * Dumper
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ScriptDumper
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var JavascriptCompressorInterface
     */
    private $compressor;

    /**
     * @var string
     */
    private $webDir;

    /**
     * @param LoggerInterface               $logger
     * @param JavascriptCompressorInterface $compressor
     * @param string                        $webDir
     */
    public function __construct(LoggerInterface $logger, JavascriptCompressorInterface $compressor, $webDir)
    {
        $this->logger = $logger;
        $this->compressor = $compressor;
        $this->webDir = $webDir;
    }

    /**
     * Dump collection and return file name
     *
     * @param Block  $block
     * @param string $outUri
     *
     * @return string
     */
    public function dump(Block $block, $outUri)
    {
        $outFilename = $this->webDir . '/' . $outUri;

        if (file_exists($outFilename)) {
            return $outFilename;
        }

        $output = '';

        foreach ($block->getFiles() as $file) {
            $file = $this->webDir . '/' . $file;

            if (!file_exists($file)) {
                $this->logger->error('File "' . $file . '" not found.');
                continue;
            }

            $output .= file_get_contents($file) . PHP_EOL;
        }

        $output = $this->compressor->compressString($output);

        if (!file_exists(dirname($outFilename))) {
            mkdir(dirname($outFilename), 0777);
        }

        file_put_contents($outFilename, $output);
        chmod($outFilename, 0777);

        return $outFilename;
    }
}
