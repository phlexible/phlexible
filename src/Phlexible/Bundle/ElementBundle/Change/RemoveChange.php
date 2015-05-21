<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Change;

use Phlexible\Bundle\ElementBundle\Entity\ElementSource;
use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\Usage\Usage;

/**
 * Elementtype remove change
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RemoveChange extends Change
{
    /**
     * @var ElementSource[]
     */
    private $removedElementSources;

    /**
     * @param Elementtype     $elementtype
     * @param Usage[]         $usage
     * @param ElementSource[] $removedElementSources
     */
    public function __construct(Elementtype $elementtype, array $usage, array $removedElementSources)
    {
        parent::__construct($elementtype, $usage);

        $this->removedElementSources = $removedElementSources;
    }

    /**
     * @return ElementSource[]
     */
    public function getRemovedElementSources()
    {
        return $this->removedElementSources;
    }

    /**
     * {@inheritdoc}
     */
    public function getReason()
    {
        return 'Elementtype removed';
    }
}
