<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TwigRendererBundle\Extension;

use Phlexible\Bundle\MediaManagerBundle\Meta\FileMetaDataManager;
use Phlexible\Bundle\MediaManagerBundle\Meta\FileMetaSetResolver;
use Phlexible\Bundle\MediaSiteBundle\Site\SiteManager;
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
     * @var SiteManager
     */
    private $siteManager;

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
     * @param SiteManager         $siteManager
     * @param FileMetaSetResolver $metaSetResolver
     * @param FileMetaDataManager $metaDataManager
     */
    public function __construct(RouterInterface $router, SiteManager $siteManager, FileMetaSetResolver $metaSetResolver, FileMetaDataManager $metaDataManager)
    {
        $this->router = $router;
        $this->siteManager = $siteManager;
        $this->metaSetResolver = $metaSetResolver;
        $this->metaDataManager = $metaDataManager;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('image', array($this, 'image')),
            new \Twig_SimpleFunction('icon', array($this, 'icon')),
            new \Twig_SimpleFunction('thumbnail', array($this, 'thumbnail')),
            new \Twig_SimpleFunction('download', array($this, 'download')),
            new \Twig_SimpleFunction('fileinfo', array($this, 'fileinfo')),
        );
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
        $src = $this->router->generate('frontendmedia_inline', array('fileId' => $fileId));

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

        $src = $this->router->generate('frontendmedia_icon', array('fileId' => $fileId, 'size' => $size));

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
            $src = $this->router->generate('frontendmedia_inline', array('fileId' => $fileId));
        } else {
            // deliver thumbnail
            $src = $this->router->generate('frontendmedia_thumbnail', array('fileId' => $fileId, 'template' => $template));
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

        $src = $this->router->generate('frontendmedia_download', array('fileId' => $fileId));

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
            return array();
        }

        $parts = explode(';', $file);
        $fileId = $parts[0];
        $fileVersion = 1;
        if (isset($parts[1])) {
            $fileVersion = $parts[1];
        }

        $site = $this->siteManager->getByFileId($fileId, $fileVersion);
        $file = $site->findFile($fileId, $fileVersion);

        $info = array(
            'mimetype'     => $file->getMimeType(),
            'assettype'    => $file->getAttribute('assettype'),
            'documenttype' => $file->getAttribute('documenttype'),
            'size'         => $file->getSize(),
            'attributes'   => $file->getAttribute('attributes'),
            'meta'         => array(),
        );

        $metasets = $this->metaSetResolver->resolve($file);
        foreach ($metasets as $metaset) {
            $metadata = $this->metaDataManager->findByMetaSetAndFile($metaset, $file);
            if ($metadata) {
                $data = array();
                foreach ($metaset->getFields() as $field) {
                    $data[$field->getName()] = $metadata->get($field->getName(), 'de');
                }
                $info['meta'][$metaset->getName()] = $data;
            }
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