<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Change;

use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;

/**
 * Template change
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Change
{
    /**
     * @var ExtendedFileInterface
     */
    private $file;

    /**
     * @var TemplateInterface
     */
    private $template;

    /**
     * @var string
     */
    private $revision;

    /**
     * @param ExtendedFileInterface $file
     * @param TemplateInterface     $template
     * @param string                $revision
     */
    public function __construct(ExtendedFileInterface $file, TemplateInterface $template, $revision)
    {
        $this->file = $file;
        $this->template = $template;
        $this->revision = $revision;
    }

    /**
     * @return ExtendedFileInterface
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return TemplateInterface
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @return string
     */
    public function getRevision()
    {
        return $this->revision;
    }
}
