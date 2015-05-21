<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Change;

use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;

/**
 * Elementtype add change
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddChange extends Change
{
    /**
     * @var bool
     */
    private $needImport = false;

    /**
     * @param Elementtype $elementtype
     * @param bool        $needImport
     */
    public function __construct(Elementtype $elementtype, $needImport)
    {
        parent::__construct($elementtype);

        $this->needImport = $needImport;
    }

    /**
     * @return bool
     */
    public function getNeedImport()
    {
        return $this->needImport;
    }

    /**
     * {@inheritdoc}
     */
    public function getReason()
    {
        return 'Elementtype added';
    }
}
