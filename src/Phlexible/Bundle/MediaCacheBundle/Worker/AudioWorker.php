<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Worker;

use Phlexible\Bundle\DocumenttypeBundle\Model\DocumenttypeManagerInterface;
use Phlexible\Bundle\MediaAssetBundle\Transmutor;
use Phlexible\Bundle\MediaCacheBundle\CacheIdStrategy\CacheIdStrategyInterface;
use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Bundle\MediaCacheBundle\Model\CacheManagerInterface;
use Phlexible\Bundle\MediaCacheBundle\Storage\StorageManager;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;
use Phlexible\Bundle\MediaTemplateBundle\Applier\AudioTemplateApplier;
use Phlexible\Bundle\MediaTemplateBundle\Model\AudioTemplate;
use Phlexible\Bundle\MediaTemplateBundle\Model\TemplateInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Audio cache worker
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AudioWorker extends AbstractWorker
{
    /**
     * @var StorageManager
     */
    private $storageManager;

    /**
     * @var Transmutor
     */
    private $transmutor;

    /**
     * @var CacheManagerInterface
     */
    private $cacheManager;

    /**
     * @var DocumenttypeManagerInterface
     */
    private $documenttypeManager;

    /**
     * @var CacheIdStrategyInterface
     */
    private $cacheIdStrategy;

    /**
     * @var CacheIdStrategyInterface
     */
    private $applier;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $tempDir;

    /**
     * @param StorageManager               $storageManager
     * @param Transmutor                   $transmutor
     * @param CacheManagerInterface        $cacheManager
     * @param DocumenttypeManagerInterface $documenttypeManager
     * @param CacheIdStrategyInterface     $cacheIdStrategy
     * @param AudioTemplateApplier         $applier
     * @param LoggerInterface              $logger
     * @param string                       $tempDir
     */
    public function __construct(StorageManager $storageManager,
                                Transmutor $transmutor,
                                CacheManagerInterface $cacheManager,
                                DocumenttypeManagerInterface $documenttypeManager,
                                CacheIdStrategyInterface $cacheIdStrategy,
                                AudioTemplateApplier $applier,
                                LoggerInterface $logger,
                                $tempDir)
    {
        $this->storageManager = $storageManager;
        $this->transmutor = $transmutor;
        $this->cacheManager = $cacheManager;
        $this->documenttypeManager = $documenttypeManager;
        $this->cacheIdStrategy = $cacheIdStrategy;
        $this->applier = $applier;
        $this->logger = $logger;
        $this->tempDir = $tempDir;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(TemplateInterface $template, FileInterface $file)
    {
        return $template instanceof AudioTemplate && strtolower($file->getAttribute('assettype')) === 'audio';
    }

    /**
     * {@inheritdoc}
     */
    public function process(TemplateInterface $template, FileInterface $file)
    {
        $audioFile = $this->transmutor->transmuteToAudio($file);

        return $this->work($template, $file, $audioFile);
    }

    /**
     * Apply template to filename
     *
     * @param AudioTemplate $template
     * @param FileInterface $file
     * @param string        $inputFilename
     *
     * @return CacheItem
     */
    private function work(AudioTemplate $template, FileInterface $file, $inputFilename)
    {
        $site        = $file->getSite();
        $fileId      = $file->getId();
        $fileVersion = $file->getVersion();

        $cacheId      = $this->cacheIdStrategy->createCacheId($template, $file);
        $tempFilename = $this->tempDir . '/' . $cacheId . '.' . $template->getParameter('audio_format');

        $cacheItem = $this->cacheManager->find($cacheId);
        if (!$cacheItem) {
            $cacheItem = new CacheItem();
            $cacheItem->setId($cacheId);
        }

        $cacheItem
            ->setSiteId($site->getId())
            ->setFileId($fileId)
            ->setFileVersion($fileVersion)
            ->setTemplateKey($template->getKey())
            ->setTemplateRevision($template->getRevision())
            ->setStatus(CacheItem::STATUS_DELEGATE)
            ->setMimeType($file->getMimeType())
            ->setDocumentTypeKey(strtolower($file->getAttribute('documenttype')))
            ->setExtension('')
            ->setFileSize(0)
            ->setError(null)
            ->setCreatedAt(new \DateTime());

        if (!file_exists($inputFilename)) {
            $this->applyError($cacheItem, CacheItem::STATUS_MISSING, 'Input file not found.', $inputFilename, $template, $file);
        } elseif ($this->applier->isAvailable($inputFilename)) {
            $this->applyError($cacheItem, CacheItem::STATUS_MISSING, 'No suitable audio template applier found.', $inputFilename, $template, $file);
        } else {
            $filesystem = new Filesystem();
            if (!$filesystem->exists($this->tempDir)) {
                $filesystem->mkdir($this->tempDir, 0777);
            }
            if (!$filesystem->exists($tempFilename)) {
                $filesystem->remove($tempFilename);
            }

            try {
                $this->applier->apply($template, $inputFilename, $tempFilename);

                $filesystem->chmod($tempFilename, 0777);

                $fileInfo = $this->documenttypeManager->getMimeDetector()->detect($tempFilename);
                $documentType = $this->documenttypeManager->findByMimetype($fileInfo->getMimeType());

                $cacheItem
                    ->setStatus(CacheItem::STATUS_OK)
                    ->setMimeType($fileInfo->getMimeType())
                    ->setDocumentTypeKey($documentType->getKey())
                    ->setExtension($fileInfo->getExtension())
                    ->setFilesize($fileInfo->getSize());
            } catch (\Exception $e) {
                $cacheItem
                    ->setStatus(CacheItem::STATUS_ERROR)
                    ->setError($e);
            }

            if ($cacheItem->getStatus() === CacheItem::STATUS_OK) {
                $storage = $this->storageManager->getStorage($template->getStorage());
                $storage->store($cacheItem, $tempFilename);
            }
        }

        $this->cacheManager->updateCacheItem($cacheItem);

        if ($cacheItem->getError()) {
            $this->logger->error($cacheItem->getError());
        }

        return $cacheItem;
    }
}