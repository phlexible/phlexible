<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\ImageDelegate;

use Phlexible\Bundle\DocumenttypeBundle\Model\Documenttype;
use Phlexible\Bundle\DocumenttypeBundle\Model\DocumenttypeManagerInterface;
use Phlexible\Bundle\DocumenttypeBundle\Model\IconResolver;
use Phlexible\Bundle\MediaCacheBundle\Exception\CreateDelegateFailed;
use Phlexible\Bundle\MediaManagerBundle\Entity\File;
use Phlexible\Bundle\MediaTemplateBundle\Applier\ImageTemplateApplier;
use Phlexible\Bundle\MediaTemplateBundle\Model\ImageTemplate;
use Phlexible\Bundle\MediaTemplateBundle\Model\TemplateManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * Delegate worker
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DelegateWorker
{
    /**
     * @var TemplateManagerInterface
     */
    private $templateManager;

    /**
     * @var DocumenttypeManagerInterface
     */
    private $documenttypeManager;

    /**
     * @var ImageTemplateApplier
     */
    private $applier;

    /**
     * @var FileLocator
     */
    private $locator;

    /**
     * @var string
     */
    private $delegateDirClean;

    /**
     * @var string
     */
    private $delegateDirWaiting;

    /**
     * @param TemplateManagerInterface     $templateManager
     * @param DocumenttypeManagerInterface $documenttypeManager
     * @param ImageTemplateApplier         $applier
     * @param FileLocator                  $locator
     * @param string                       $delegateDir
     */
    public function __construct(
        TemplateManagerInterface $templateManager,
        DocumenttypeManagerInterface $documenttypeManager,
        ImageTemplateApplier $applier,
        FileLocator $locator,
        $delegateDir
    ) {
        $this->templateManager = $templateManager;
        $this->documenttypeManager = $documenttypeManager;
        $this->applier = $applier;
        $this->locator = $locator;

        $this->delegateDirClean   = $delegateDir . 'clean/';
        $this->delegateDirWaiting = $delegateDir . 'waiting/';
    }

    /**
     * @param bool     $force
     * @param callable $callback
     */
    public function writeAll($force = false, callable $callback = null)
    {
        $templates  = $this->templateManager->findBy(['type' => 'image']);
        $documentTypes = $this->documenttypeManager->findAll();

        $cnt = count($templates) * count($documentTypes);

        if (is_callable($callback)) {
            call_user_func($callback, 'count', $cnt);
        }

        foreach ($templates as $template) {
            foreach ($documentTypes as $documentType) {
                $this->write($documentType, $template, $force);

                if (is_callable($callback)) {
                    call_user_func($callback, 'update', $template->getKey(), $documentType->getKey());
                }
            }
        }
    }

    /**
     * @param ImageTemplate $template
     *
     * @return string
     */
    public function getCleanDir(ImageTemplate $template)
    {
        return $this->delegateDirClean . $template->getKey() . '/';
    }

    /**
     * @param ImageTemplate $template
     *
     * @return string
     */
    public function getWaitingDir(ImageTemplate $template)
    {
        return $this->delegateDirWaiting . $template->getKey() . '/';
    }

    /**
     * @param ImageTemplate $template
     * @param Documenttype  $documenttype
     *
     * @return string
     */
    public function getCleanFilename(ImageTemplate $template, Documenttype $documenttype)
    {
        return $this->getCleanDir($template) . $documenttype->getKey() . '.gif';
    }

    /**
     * @param ImageTemplate $template
     * @param Documenttype  $documenttype
     *
     * @return string
     */
    public function getWaitingFilename(ImageTemplate $template, Documenttype $documenttype)
    {
        return $this->getWaitingDir($template) . $documenttype->getKey() . '.gif';
    }

    /**
     * @param Documenttype $documenttype
     *
     * @return string
     */
    public function getFilename(Documenttype $documenttype)
    {
        return $documenttype->getKey() . '.gif';
    }

    /**
     * @param Documenttype  $documentType
     * @param ImageTemplate $template
     * @param bool          $force
     *
     * @throws CreateDelegateFailed
     */
    public function write(Documenttype $documentType, ImageTemplate $template, $force = false)
    {
        $templateModifyTime = $template->getModifiedAt()->format('U');

        $templateWidth = $template->getWidth();
        if (!$templateWidth) {
            $templateWidth = 256;
        }
        $iconResolver = new IconResolver();
        $icon = $iconResolver->resolve($templateWidth);

        if (!$icon || !file_exists($icon)) {
            return;
        }

        $filePathClean   = $this->getCleanFilename($template, $documentType);
        $filePathWaiting = $this->getWaitingFilename($template, $documentType);

        $dirClean   = dirname($filePathClean);
        $dirWaiting = dirname($filePathWaiting);

        $filesystem = new Filesystem();

        if (!$filesystem->exists($dirClean)) {
            $filesystem->mkdir($dirClean);
        }
        if (!$filesystem->exists($dirWaiting)) {
            $filesystem->mkdir($dirWaiting);
        }

        if ($force || !$filesystem->exists($filePathClean)
                || !filesize($filePathClean)
                || filemtime($filePathClean) < $templateModifyTime) {
            $this->applier->apply($template, new File(), $icon, $filePathClean);

            if (!$filesystem->exists($filePathClean)) {
                throw new CreateDelegateFailed('"Clean" delegate image not created: ' . $filePathClean);
            }
        }

        if ($force || !$filesystem->exists($filePathWaiting)
                || !filesize($filePathWaiting)
            || filemtime($filePathWaiting) < $templateModifyTime) {
            $source = imagecreatefromgif($filePathClean);
            $sx = imagesx($source);
            $sy = imagesy($source);
            $target = imagecreatetruecolor($sx, $sy);
            imagealphablending($target, true);
            imagesavealpha($target, true);
            $transparent = imagecolorallocatealpha($target, 255, 255, 255, 127);
            imagefilledrectangle($target, 0, 0, $sx, $sy, $transparent);
            imagecopy($target, $source, 0, 0, 0, 0, $sx, $sy);
            $waiting = imagecreatefrompng(
                $this->locator->locate('@PhlexibleMediaCacheBundle/Resources/public/icons/waiting.png')
            );
            $sx -= 16;
            $sy -= 16;
            imagecopy($target, $waiting, $sx, $sy, 0, 0, 16, 16);
            // $red   = imagecolorallocate($target, 235, 235, 235);
            // imagerectangle($target, $sx - 0, $sy - 1, $sx + 15, $sy + 16, $red);
            imagegif($target, $filePathWaiting);
            imagedestroy($source);
            imagedestroy($target);
            imagedestroy($waiting);

            if (!$filesystem->exists($filePathWaiting)) {
                throw new CreateDelegateFailed('"Waiting" delegate image not created: ' . $filePathWaiting);
            }
        }
    }
}
