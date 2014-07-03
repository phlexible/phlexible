<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\Model;

/**
 * Pdf template
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PdfTemplate extends AbstractTemplate
{
    const TYPE_PDF = 'pdf';

    public function __construct()
    {
        $this->setType(self::TYPE_PDF);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultParameters()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedParameters()
    {
        return array(
            'stop',
            'quality',
            'same_window',
            'flash_version',
            'page_range',
            'fonts',
            'flatten',
            'viewer',
            'default_viewer',
        );
    }
}
