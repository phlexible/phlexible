<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Change;

use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;
use Phlexible\Bundle\MediaTemplateBundle\Model\TemplateInterface;

/**
 * Template change
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Change
{
    /**
     * @var FileInterface
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
     * @param FileInterface     $file
     * @param TemplateInterface $template
     * @param string            $revision
     */
    public function __construct(FileInterface $file, TemplateInterface $template, $revision)
    {
        $this->file = $file;
        $this->template = $template;
        $this->revision = $revision;
    }

    /**
     * @return FileInterface
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
