<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DocumenttypeBundle\File;

use Brainbits\Mime\MimeDetector;
use Phlexible\Bundle\DocumenttypeBundle\Exception\NotFoundException;
use Phlexible\Bundle\DocumenttypeBundle\Model\DocumenttypeCollection;
use Phlexible\Bundle\DocumenttypeBundle\Model\DocumenttypeManagerInterface;

/**
 * Documenttype manager
 *
 * @package Phlexible\Bundle\DocumenttypeBundle\Documenttype
 */
class DocumenttypeManager implements DocumenttypeManagerInterface
{
    /**
     * @var DocumenttypeLoader
     */
    private $documenttypeLoader;

    /**
     * @var MimeDetector
     */
    private $mimeDetector;

    /**
     * @var DocumenttypeCollection
     */
    private $documenttypes;

    /**
     * @param DocumenttypeLoader $documenttypeLoader
     * @param MimeDetector       $mimeDetector
     */
    public function __construct(DocumenttypeLoader $documenttypeLoader, MimeDetector $mimeDetector)
    {
        $this->documenttypeLoader = $documenttypeLoader;
        $this->mimeDetector = $mimeDetector;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->getCollection()->create();
    }

    /**
     * @return DocumenttypeCollection
     */
    public function getCollection()
    {
        if ($this->documenttypes === null) {
            $this->documenttypes = $this->documenttypeLoader->loadDocumenttypes();
        }

        return $this->documenttypes;
    }

    /**
     * @return MimeDetector
     */
    public function getMimeDetector()
    {
        return $this->mimeDetector;
    }

    /**
     * {@inheritdoc}
     */
    public function find($key)
    {
        $documentType = $this->getCollection()->get($key);

        if ($documentType !== null) {
            return $documentType;
        }

        throw new NotFoundException('Documenttype key "' . $key . '" not found.');
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->getCollection()->getAll();
    }

    /**
     * {@inheritdoc}
     */
    public function findByMimetype($mimetype)
    {
        $documentType = $this->getCollection()->getByMimetype($mimetype);

        if ($documentType !== null) {
            return $documentType;
        }

        throw new NotFoundException('Documenttype for mimetype "' . $mimetype . '" not found.');
    }

    /**
     * {@inheritdoc}
     */
    public function findKeyByMimetype($mimetype)
    {
        $documentType = $this->findByMimetype($mimetype);

        return $documentType->getKey();
    }

    /**
     * {@inheritdoc}
     */
    public function findByFilename($filename)
    {
        $mimetype = $this->mimeDetector->detect($filename, MimeDetector::RETURN_STRING);

        return $this->findByMimetype($mimetype);
    }
}
