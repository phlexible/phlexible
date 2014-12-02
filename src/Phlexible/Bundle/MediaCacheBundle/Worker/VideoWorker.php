<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Worker;

use FFMpeg\FFProbe;
use Phlexible\Bundle\DocumenttypeBundle\Model\DocumenttypeManagerInterface;
use Phlexible\Bundle\MediaCacheBundle\CacheIdStrategy\CacheIdStrategyInterface;
use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Bundle\MediaCacheBundle\Model\CacheManagerInterface;
use Phlexible\Bundle\MediaCacheBundle\Storage\StorageManager;
use Phlexible\Bundle\MediaExtractorBundle\Transmutor;
use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;
use Phlexible\Bundle\MediaTemplateBundle\Applier\VideoTemplateApplier;
use Phlexible\Bundle\MediaTemplateBundle\Model\TemplateInterface;
use Phlexible\Bundle\MediaTemplateBundle\Model\VideoTemplate;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Video cache worker
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class VideoWorker extends AbstractWorker
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
     * @var VideoTemplateApplier
     */
    private $applier;

    /**
     * @var FFProbe
     */
    private $analyzer;

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
     * @param VideoTemplateApplier         $applier
     * @param FFProbe                      $analyzer
     * @param LoggerInterface              $logger
     * @param string                       $tempDir
     */
    public function __construct(StorageManager $storageManager,
                                Transmutor $transmutor,
                                CacheManagerInterface $cacheManager,
                                DocumenttypeManagerInterface $documenttypeManager,
                                CacheIdStrategyInterface $cacheIdStrategy,
                                VideoTemplateApplier $applier,
                                FFProbe $analyzer,
                                LoggerInterface $logger,
                                $tempDir)
    {
        $this->storageManager = $storageManager;
        $this->transmutor = $transmutor;
        $this->cacheManager = $cacheManager;
        $this->documenttypeManager = $documenttypeManager;
        $this->cacheIdStrategy = $cacheIdStrategy;
        $this->applier = $applier;
        $this->analyzer = $analyzer;
        $this->logger = $logger;
        $this->tempDir = $tempDir;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(TemplateInterface $template, FileInterface $file)
    {
        return $template instanceof VideoTemplate && strtolower($file->getAssettype()) === 'video';
    }

    /**
     * {@inheritdoc}
     */
    public function process(TemplateInterface $template, FileInterface $file)
    {
        $videoFile = $this->transmutor->transmuteToVideo($file);

        return $this->work($template, $file, $videoFile);
    }

    /**
     * Apply template to filename
     *
     * @param VideoTemplate $template
     * @param FileInterface $file
     * @param string        $inputFilename
     *
     * @return CacheItem
     */
    private function work(VideoTemplate $template, FileInterface $file, $inputFilename)
    {
        $site        = $file->getSite();
        $fileId      = $file->getID();
        $fileVersion = $file->getVersion();

        $cacheId      = $this->cacheIdStrategy->createCacheId($template, $file);
        $tempFilename = $this->tempDir . '/' . $cacheId . '.' . $template->getParameter('video_format', 'flv');

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
            ->setCacheStatus(CacheItem::STATUS_DELEGATE)
            ->setMimeType($file->getMimeType())
            ->setDocumentTypeKey(strtolower($file->getDocumenttype()))
            ->setExtension('')
            ->setFileSize(0)
            ->setError(null)
            ->setCreatedAt(new \DateTime());

        if (!file_exists($inputFilename)) {
            $this->applyError($cacheItem, CacheItem::STATUS_MISSING, 'Input file not found.', $inputFilename, $template, $file);
        } elseif (!$this->applier->isAvailable($inputFilename)) {
            $this->applyError($cacheItem, CacheItem::STATUS_MISSING, 'No suitable video template applier found.', $inputFilename, $template, $file);
        } else {
            $filesystem = new Filesystem();
            if (!$filesystem->exists($this->tempDir)) {
                $filesystem->mkdir($this->tempDir, 0777);
            }
            if (!$filesystem->exists($tempFilename)) {
                $filesystem->remove($tempFilename);
            }

            try {
                $matchFormat = $template->hasParameter('match_format') ? $template->getParameter('match_format') : false;
                if ($matchFormat && strtolower($file->getDocumenttype()) === strtolower($template->getParameter('format'))) {
                    $tempFilename = $inputFilename;
                } else {
                    $this->applier->apply($template, $inputFilename, $tempFilename);
                }

                $videoStream = $this->analyzer->streams($tempFilename)->videos()->first();
                $width = $videoStream->getDimensions()->getWidth();
                $height = $videoStream->getDimensions()->getHeight();

                $filesystem->chmod($tempFilename, 0777);

                $fileInfo = $this->documenttypeManager->getMimeDetector()->detect($tempFilename);
                $documentType = $this->documenttypeManager->findByMimetype($fileInfo->getMimeType());

                $cacheItem
                    ->setCacheStatus(CacheItem::STATUS_OK)
                    ->setMimeType($fileInfo->getMimeType())
                    ->setDocumentTypeKey($documentType->getKey())
                    ->setExtension($fileInfo->getExtension())
                    ->setFilesize($fileInfo->getSize())
                    ->setWidth($width)
                    ->setHeight($height);
            } catch (\Exception $e) {
                $cacheItem
                    ->setCacheStatus(CacheItem::STATUS_ERROR)
                    ->setError($e);
            }

            if ($cacheItem->getCacheStatus() === CacheItem::STATUS_OK) {
                $storage = $this->storageManager->get($template->getStorage());
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
