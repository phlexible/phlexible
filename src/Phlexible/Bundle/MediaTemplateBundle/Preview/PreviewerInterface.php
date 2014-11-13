<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\Preview;

/**
 * Previewer interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface PreviewerInterface
{
    /**
     * @return string
     */
    public function getPreviewDir();

    /**
     * @param array $params
     *
     * @return array
     */
    public function create(array $params);
}
