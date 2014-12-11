<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaTemplate\Model;

/**
 * Pdf template
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PdfTemplate extends AbstractTemplate
{
    const TYPE_PDF = 'pdf';

    /**
     * Constructor.
     */
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
            'jpeg_quality',
            'pages',
            'zlib_enable',
            'simpleviewer_enable',
            'links_new_window',
            'links_disable',
            'resolution',
            'framerate',
            'viewer',
        );
    }
}
