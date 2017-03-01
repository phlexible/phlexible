<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Mime\Adapter;

use Phlexible\Component\Mime\Exception\DetectionFailedException;
use Phlexible\Component\Mime\Exception\FileNotFoundException;
use Phlexible\Component\Mime\Exception\NotAFileException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Internet media type detector file adapter.
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class FileAdapter implements AdapterInterface
{
    /**
     * @var string
     */
    private $fileCommand = null;

    /**
     * @param string $fileCommand
     */
    public function __construct($fileCommand)
    {
        $this->fileCommand = $fileCommand;
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable($filename)
    {
        $processBuilder = $this->createBaseProcessBuilder()
            ->add(__FILE__);

        $process = $processBuilder->getProcess();
        $process->run();

        return $process->isSuccessful();
    }

    /**
     * {@inheritdoc}
     */
    public function getInternetMediaTypeStringFromFile($filename)
    {
        if (!file_exists($filename)) {
            throw new FileNotFoundException('File "'.$filename.'" not found.');
        }

        if (!is_file($filename)) {
            throw new NotAFileException('File "'.$filename.'" not found.');
        }

        $file = preg_replace(['/\(/', '/\)/'], ['(', ')'], $filename);

        // exec file command in shell
        $processBuilder = $this->createBaseProcessBuilder()
            ->add('-b')
            ->add('--mime')
            ->add($file);

        $process = $processBuilder->getProcess();
        $rc = $process->run();

        if (!$process->isSuccessful()) {
            $msg = 'File command '.$process->getCommandLine().' returned unsuccessfully, '.
                'rc: '.$rc.', '.
                'output: '.$process->getOutput().', '.
                'error: '.$process->getErrorOutput();
            throw new DetectionFailedException($msg);
        }

        return $this->parseProcessOutput($process);
    }

    /**
     * Parse output.
     *
     * @param Process $process
     *
     * @return string
     */
    private function parseProcessOutput(Process $process)
    {
        $output = $process->getOutput();

        // Get only MIME part
        $matches = [];
        $match = preg_match('/[a-zA-z0-9\-\+\_\-]+\/[a-zA-Z0-9\-\+\_\-]+/', $output, $matches);

        // Check for errors
        if ($match) {
            return $matches[0];
        }

        return 'application/octet-stream';
    }

    /**
     * Create base process builder.
     *
     * @return ProcessBuilder
     */
    private function createBaseProcessBuilder()
    {
        $processBuilder = new ProcessBuilder([$this->fileCommand]);

        return $processBuilder;
    }
}
