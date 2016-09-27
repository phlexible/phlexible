<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Mime\Adapter;

use Phlexible\Component\Mime\Exception\DetectionFailedException;
use Phlexible\Component\Mime\Exception\FileNotFoundException;
use Phlexible\Component\Mime\Exception\NotAFileException;

/**
 * Internet media type detector fileinfo adapter
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class FileinfoAdapter implements AdapterInterface
{
    /**
     * @var resource
     */
    private $fileinfo = null;

    /**
     * @var string
     */
    private $magicFile = null;

    /**
     * @param string $magicFile
     *
     * @throws FileNotFoundException
     */
    public function __construct($magicFile = null)
    {
        if (null !== $magicFile) {
            if (!file_exists($magicFile)) {
                throw new FileNotFoundException('Magic file "' . $magicFile . '" not found.');
            }

            $this->magicFile = $magicFile;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable($filename)
    {
        // test if fileinfo extension is loaded
        if (!extension_loaded('fileinfo')) {
            return false;
        }

        $fileinfo = $this->getFileinfo();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getInternetMediaTypeStringFromFile($filename)
    {
        // test if fileinfo extension is loaded
        if (!extension_loaded('fileinfo')) {
            return false;
        }

        if (!file_exists($filename)) {
            throw new FileNotFoundException('File "' . $filename . '" not found.');
        }

        if (!is_file($filename)) {
            throw new NotAFileException('File "' . $filename . '" not found.');
        }

        $fileinfo = $this->getFileinfo();

        $internetMediaTypeString = finfo_file($fileinfo, $filename);

        return $internetMediaTypeString;
    }

    /**
     * Return fileinfo resource
     *
     * @return resource
     * @throws DetectionFailedException
     */
    private function getFileinfo()
    {
        if (null === $this->fileinfo) {
            $this->fileinfo = finfo_open(FILEINFO_MIME, $this->magicFile);

            if (false === $this->fileinfo) {
                throw new DetectionFailedException('Instanciation of fileinfo failed.');
            }
        }

        return $this->fileinfo;
    }
}
