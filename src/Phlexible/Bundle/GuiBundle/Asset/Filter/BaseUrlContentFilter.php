<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Asset\Filter;

/**
 * Filter phlexible baseurl and basepath
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BaseUrlContentFilter implements ContentFilterInterface
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $basePath;

    /**
     * @param string $baseUrl
     * @param string $basePath
     */
    public function __construct($baseUrl, $basePath)
    {
        $this->baseUrl = '/';
        $this->basePath = '/';

        if ($baseUrl && $baseUrl !== '/') {
            $this->baseUrl = '/' . trim($baseUrl, '/') . '/';
        }

        if ($basePath && $basePath !== '/') {
            $this->basePath = '/' . trim($basePath, '/') . '/';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function filter($content)
    {
        return str_replace(
            ['/BASE_PATH/', '/BASE_URL/', '/BUNDLES_PATH/'],
            [$this->basePath, $this->baseUrl, $this->basePath . 'bundles/'],
            $content
        );
    }
}
