<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaTemplate\Previewer;

/**
 * Previewer interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface PreviewerInterface
{
    /**
     * @param string $filePath
     * @param array  $params
     *
     * @return array
     */
    public function create($filePath, array $params);
}
