<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Twig\Extension;

use Phlexible\Bundle\MediaManagerBundle\Meta\FileMetaDataManager;
use Phlexible\Bundle\MediaManagerBundle\Meta\FileMetaSetResolver;
use Phlexible\Component\Volume\VolumeManager;
use Symfony\Component\Routing\RouterInterface;

/**
 * Twig media extension
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
            new \Twig_SimpleFunction('image', [$this, 'image']),
            new \Twig_SimpleFunction('icon', [$this, 'icon']),
            new \Twig_SimpleFunction('thumbnail', [$this, 'thumbnail']),
            new \Twig_SimpleFunction('download', [$this, 'download']),
            new \Twig_SimpleFunction('fileinfo', [$this, 'fileinfo']),
        ];
    }

    /**
     * @param string $image
     *
     * @return string
     */
    public function image($image)
    {
        if (!$image) {
            return '';
        }

        $parts = explode(';', $image);
        $fileId = $parts[0];
        $fileVersion = 1;
        if (isset($parts[1])) {
            $fileVersion = $parts[1];
        }

        // deliver original file
        $src = $this->router->generate('frontendmedia_inline', ['fileId' => $fileId]);

        return $src;
    }

    /**
     * @param string $image
     * @param int    $size
     *
     * @return string
     */
    public function icon($image, $size = 16)
    {
        if (!$image) {
            return '';
        }

        $parts = explode(';', $image);
        $fileId = $parts[0];
        $fileVersion = 1;
        if (isset($parts[1])) {
            $fileVersion = $parts[1];
        }

        $src = $this->router->generate('frontendmedia_icon', ['fileId' => $fileId, 'size' => $size]);

        return $src;
    }

    /**
     * @param string $image
     * @param string $template
     *
     * @return string
     */
    public function thumbnail($image, $template = null)
    {
        if (!$image && !$template) {
            return '';
        }

        $parts = explode(';', $image);
        $fileId = $parts[0];
        $fileVersion = 1;
        if (isset($parts[1])) {
            $fileVersion = $parts[1];
        }

        if ($template === null) {
            // deliver original file
            $src = $this->router->generate('frontendmedia_inline', ['fileId' => $fileId]);
        } else {
            // deliver thumbnail
            $src = $this->router->generate('frontendmedia_thumbnail', ['fileId' => $fileId, 'template' => $template]);
        }

        return $src;
    }

    /**
     * @param string $file
     *
     * @return string
     */
    public function download($file)
    {
        if (!$file) {
            return '';
        }

        $parts = explode(';', $file);
        $fileId = $parts[0];
        $fileVersion = 1;
        if (isset($parts[1])) {
            $fileVersion = $parts[1];
        }

        $src = $this->router->generate('frontendmedia_download', ['fileId' => $fileId]);

        return $src;
    }

    /**
     * @param string $file
     *
     * @return array
     */
    public function fileinfo($file)
    {
        if (!$file) {
            return [];
        }

        $parts = explode(';', $file);
        $fileId = $parts[0];
        $fileVersion = 1;
        if (isset($parts[1])) {
            $fileVersion = $parts[1];
        }

        $volume = $this->volumeManager->getByFileId($fileId, $fileVersion);
        $file = $volume->findFile($fileId, $fileVersion);

        $info = [
            'name'          => $file->getName(),
            'mimetype'      => $file->getMimeType(),
            'mediaCategory' => $file->getMediaCategory(),
            'mediaType'     => $file->getMediaType(),
            'size'          => $file->getSize(),
            'attributes'    => $file->getAttributes(),
            'createdAt'     => $file->getCreatedAt(),
            'modifiedAt'    => $file->getModifiedAt(),
            'meta'          => [],
        ];

        $metasets = $this->metaSetResolver->resolve($file);
        foreach ($metasets as $metaset) {
            $metadata = $this->metaDataManager->findByMetaSetAndFile($metaset, $file);
            $data = [];
            foreach ($metaset->getFields() as $field) {
                $value = '';
                if ($metadata) {
                    $value = $metadata->get($field->getName(), 'de');
                }
                $data[$field->getName()] = $value;
            }
            $info['meta'][$metaset->getName()] = $data;
        }

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
