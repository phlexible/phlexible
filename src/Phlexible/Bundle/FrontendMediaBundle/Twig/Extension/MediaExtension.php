<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\FrontendMediaBundle\Twig\Extension;

use Phlexible\Bundle\FrontendMediaBundle\Meta\MetaSetAccessor;
use Phlexible\Component\MediaManager\Meta\FileMetaDataManager;
use Phlexible\Component\MediaManager\Meta\FileMetaSetResolver;
use Phlexible\Component\Volume\VolumeManager;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Twig media extension.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaExtension extends \Twig_Extension
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var VolumeManager
     */
    private $volumeManager;

    /**
     * @var FileMetaSetResolver
     */
    private $metaSetResolver;

    /**
     * @var FileMetaDataManager
     */
    private $metaDataManager;

    /**
     * @param RouterInterface     $router
     * @param VolumeManager       $volumeManager
     * @param FileMetaSetResolver $metaSetResolver
     * @param FileMetaDataManager $metaDataManager
     */
    public function __construct(RouterInterface $router, VolumeManager $volumeManager, FileMetaSetResolver $metaSetResolver, FileMetaDataManager $metaDataManager)
    {
        $this->router = $router;
        $this->volumeManager = $volumeManager;
        $this->metaSetResolver = $metaSetResolver;
        $this->metaDataManager = $metaDataManager;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('image_url', [$this, 'imageUrl']),
            new \Twig_SimpleFunction('image_path', [$this, 'imagePath']),
            new \Twig_SimpleFunction('icon_url', [$this, 'iconUrl']),
            new \Twig_SimpleFunction('icon_path', [$this, 'iconPath']),
            new \Twig_SimpleFunction('thumbnail_url', [$this, 'thumbnailUrl']),
            new \Twig_SimpleFunction('thumbnail_path', [$this, 'thumbnailPath']),
            new \Twig_SimpleFunction('download_url', [$this, 'downloadUrl']),
            new \Twig_SimpleFunction('download_path', [$this, 'downloadPath']),
            new \Twig_SimpleFunction('fileinfo', [$this, 'fileinfo']),
        ];
    }

    /**
     * @param string $image
     *
     * @return string
     */
    public function imageUrl($image)
    {
        if (!$image) {
            return '';
        }

        $parts = explode(';', $image);
        $fileId = $parts[0];

        // deliver original file
        $src = $this->router->generate('frontendmedia_inline', ['fileId' => $fileId], UrlGeneratorInterface::ABSOLUTE_URL);

        return $src;
    }

    /**
     * @param string $image
     *
     * @return string
     */
    public function imagePath($image)
    {
        if (!$image) {
            return '';
        }

        $parts = explode(';', $image);
        $fileId = $parts[0];

        // deliver original file
        $src = $this->router->generate('frontendmedia_inline', ['fileId' => $fileId], UrlGeneratorInterface::ABSOLUTE_PATH);

        return $src;
    }

    /**
     * @param string $image
     * @param int    $size
     *
     * @return string
     */
    public function iconUrl($image, $size = 16)
    {
        if (!$image) {
            return '';
        }

        $parts = explode(';', $image);
        $fileId = $parts[0];

        $src = $this->router->generate('frontendmedia_icon', ['fileId' => $fileId, 'size' => $size], UrlGeneratorInterface::ABSOLUTE_URL);

        return $src;
    }

    /**
     * @param string $image
     * @param int    $size
     *
     * @return string
     */
    public function iconPath($image, $size = 16)
    {
        if (!$image) {
            return '';
        }

        $parts = explode(';', $image);
        $fileId = $parts[0];

        $src = $this->router->generate('frontendmedia_icon', ['fileId' => $fileId, 'size' => $size], UrlGeneratorInterface::ABSOLUTE_PATH);

        return $src;
    }

    /**
     * @param string $image
     * @param string $template
     *
     * @return string
     */
    public function thumbnailUrl($image, $template)
    {
        if (!$image || !$template) {
            return '';
        }

        $parts = explode(';', $image);
        $fileId = $parts[0];

        $src = $this->router->generate('frontendmedia_thumbnail', ['fileId' => $fileId, 'template' => $template], UrlGeneratorInterface::ABSOLUTE_URL);

        return $src;
    }

    /**
     * @param string $image
     * @param string $template
     *
     * @return string
     */
    public function thumbnailPath($image, $template)
    {
        if (!$image || !$template) {
            return '';
        }

        $parts = explode(';', $image);
        $fileId = $parts[0];

        $src = $this->router->generate('frontendmedia_thumbnail', ['fileId' => $fileId, 'template' => $template], UrlGeneratorInterface::ABSOLUTE_PATH);

        return $src;
    }

    /**
     * @param string $file
     *
     * @return string
     */
    public function downloadUrl($file)
    {
        if (!$file) {
            return '';
        }

        $parts = explode(';', $file);
        $fileId = $parts[0];

        $src = $this->router->generate('frontendmedia_download', ['fileId' => $fileId], UrlGeneratorInterface::ABSOLUTE_URL);

        return $src;
    }

    /**
     * @param string $file
     *
     * @return string
     */
    public function downloadPath($file)
    {
        if (!$file) {
            return '';
        }

        $parts = explode(';', $file);
        $fileId = $parts[0];

        $src = $this->router->generate('frontendmedia_download', ['fileId' => $fileId], UrlGeneratorInterface::ABSOLUTE_PATH);

        return $src;
    }

    /**
     * @param string $file
     * @param string $language
     *
     * @return array
     */
    public function fileinfo($file, $language = 'de')
    {
        if (!$file) {
            return [];
        }

        $parts = explode(';', $file);
        $fileId = $parts[0];

        $volume = $this->volumeManager->getByFileId($fileId);
        $file = $volume->findFile($fileId);

        if (!$file) {
            return [];
        }

        $attributes = $file->getAttributes();

        $info = [
            'name' => $file->getName(),
            'mimetype' => $file->getMimeType(),
            'mediaCategory' => $file->getMediaCategory(),
            'mediaType' => $file->getMediaType(),
            'size' => $file->getSize(),
            'attributes' => !empty($attributes['fileattributes']) ? $attributes['fileattributes'] : array(),
            'createdAt' => $file->getCreatedAt(),
            'modifiedAt' => $file->getModifiedAt(),
            'meta' => [],
        ];

        $metasets = $this->metaSetResolver->resolve($file);
        $meta = array();
        foreach ($metasets as $metaset) {
            $metadata = $this->metaDataManager->findByMetaSetAndFile($metaset, $file);
            $data = [];
            foreach ($metaset->getFields() as $field) {
                $value = '';
                if ($metadata) {
                    $value = $metadata->get($field->getName(), $language);
                }
                $data[$field->getName()] = $value;
            }
            $meta[$metaset->getName()] = $data;
        }

        $info['meta'] = new MetaSetAccessor($meta);

        return $info;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'phlexible_media';
    }
}
