<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DocumenttypeBundle\Compiler;

use Phlexible\Bundle\DocumenttypeBundle\Model\DocumenttypeCollection;

/**
 * Compiler interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface CompilerInterface
{
    /**
     * @return string
     */
    public function getClassname();

    /**
     * @param DocumenttypeCollection $documenttypes
     *
     * @return string
     */
    public function compile(DocumenttypeCollection $documenttypes);
}